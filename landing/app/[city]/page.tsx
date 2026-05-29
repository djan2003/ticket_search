import type { Metadata } from "next";
import Link from "next/link";
import { notFound } from "next/navigation";
import {
  DESTINATIONS,
  destinationBySlug,
} from "../../lib/destinations";
import { getFeed, allFlights, flightsToDestination } from "../../lib/feed";
import { FlightTable } from "../../components/FlightTable";
import { TelegramCTA } from "../../components/TelegramCTA";
import { Faq } from "../../components/Faq";
import { formatPrice } from "../../lib/format";

type Params = { params: Promise<{ city: string }> };

// Rendered on demand: reads the live feed each request (see lib/feed.ts).
export const dynamic = "force-dynamic";

export async function generateMetadata({ params }: Params): Promise<Metadata> {
  const { city } = await params;
  const dest = destinationBySlug(city);
  if (!dest) return {};

  const year = new Date().getFullYear();
  const title = `Дешёвые билеты в ${dest.nameRu} из Батуми, Тбилиси и Еревана ${year}`;
  const description = `Самые низкие цены на авиабилеты в ${dest.nameRu} (${dest.countryRu}) на выходные из Батуми, Тбилиси и Еревана: вылет в пятницу/субботу, возврат в воскресенье/понедельник. Обновляется ежедневно.`;

  return {
    title,
    description,
    alternates: { canonical: `/${dest.slug}` },
    openGraph: { title, description },
  };
}

export default async function CityPage({ params }: Params) {
  const { city } = await params;
  const dest = destinationBySlug(city);
  if (!dest) notFound();

  const feed = await getFeed();
  const flights = flightsToDestination(allFlights(feed), dest.iata);
  const cheapest = flights[0];
  const year = new Date().getFullYear();

  const others = DESTINATIONS.filter((d) => d.slug !== dest.slug).slice(0, 12);

  return (
    <div className="mx-auto max-w-5xl px-4 py-10 space-y-12">
      <nav className="text-sm text-slate-500">
        <Link href="/" className="hover:underline">
          Главная
        </Link>{" "}
        / {dest.nameRu}
      </nav>

      <section>
        <h1 className="text-3xl font-extrabold tracking-tight">
          Билеты в {dest.nameRu} на выходные {year}
        </h1>
        <p className="mt-4 max-w-2xl text-lg text-slate-600">
          {dest.blurb} Ищем самые дешёвые билеты в {dest.nameRu} (
          {dest.countryRu}) из Батуми, Тбилиси и Еревана на короткую поездку:
          вылет в пятницу или субботу, возврат в воскресенье или понедельник.
          Обновляем каждый день.
        </p>
        {cheapest && (
          <p className="mt-4 inline-block rounded-lg bg-emerald-50 px-4 py-2 font-semibold text-emerald-700">
            Сейчас от {formatPrice(cheapest.price, cheapest.currency)}{" "}
            туда-обратно на выходные
          </p>
        )}
      </section>

      <section>
        <h2 className="text-2xl font-bold">Найденные билеты в {dest.nameRu}</h2>
        <div className="mt-6">
          <FlightTable flights={flights} linkDestination={false} />
        </div>
      </section>

      <TelegramCTA />

      <section>
        <h2 className="text-2xl font-bold">Частые вопросы</h2>
        <div className="mt-6">
          <Faq
            items={[
              {
                q: `Нужна ли виза в ${dest.countryRu}?`,
                a: `Мы подбираем направления, безвизовые для путешественников из Грузии и Армении. Перед поездкой уточните актуальные правила въезда в ${dest.countryRu} для вашего паспорта.`,
              },
              {
                q: `Как купить билет в ${dest.nameRu}?`,
                a: "Нажмите «Купить» напротив подходящего варианта — откроется поиск Aviasales с этими датами, где можно оформить билет.",
              },
              {
                q: "Почему цены меняются?",
                a: "Авиабилеты дорожают и дешевеют в реальном времени. Мы показываем лучшую цену на момент последнего обновления — раз в день.",
              },
            ]}
          />
        </div>
      </section>

      <section>
        <h2 className="text-xl font-bold">Другие направления</h2>
        <div className="mt-4 flex flex-wrap gap-2">
          {others.map((d) => (
            <Link
              key={d.slug}
              href={`/${d.slug}`}
              className="rounded-full border border-slate-200 bg-white px-3 py-1.5 text-sm hover:border-sky-300 hover:text-sky-600"
            >
              {d.nameRu}
            </Link>
          ))}
        </div>
      </section>
    </div>
  );
}
