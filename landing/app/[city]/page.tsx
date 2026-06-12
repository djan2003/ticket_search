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
import { Breadcrumbs } from "../../components/Breadcrumbs";
import { PriceCalendar } from "../../components/PriceCalendar";
import { formatPrice } from "../../lib/format";
import { priceMetaDescription } from "../../lib/seo";
import { getMonthlyPrices } from "../../lib/calendar";
import { originName } from "../../lib/destinations";

type Params = { params: Promise<{ city: string }> };

// Rendered on demand: reads the live feed each request (see lib/feed.ts).
export const dynamic = "force-dynamic";

export async function generateMetadata({ params }: Params): Promise<Metadata> {
  const { city } = await params;
  const dest = destinationBySlug(city);
  if (!dest) return {};

  const year = new Date().getFullYear();
  const title = `Дешёвые авиабилеты в ${dest.nameRu} из Батуми, Тбилиси и Еревана ${year}`;
  const cheapest = flightsToDestination(allFlights(await getFeed()), dest.iata)[0];
  const description = priceMetaDescription(
    `Дешёвые авиабилеты в ${dest.nameRu} (${dest.countryRu}) на выходные из Батуми, Тбилиси и Еревана`,
    cheapest?.price ?? null,
    cheapest?.currency ?? "USD",
  );

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

  // Price-by-month chart for the origin with the cheapest current flight.
  const calendarOrigin = cheapest?.origin ?? "BUS";
  const monthlyPrices = await getMonthlyPrices(calendarOrigin, dest.iata);

  const others = DESTINATIONS.filter((d) => d.slug !== dest.slug).slice(0, 12);

  return (
    <div className="mx-auto max-w-5xl px-4 py-10 space-y-12">
      <Breadcrumbs
        items={[
          { name: "Главная", path: "/" },
          { name: dest.nameRu, path: `/${dest.slug}` },
        ]}
      />

      <section>
        <h1 className="text-3xl font-extrabold tracking-tight">
          Авиабилеты в {dest.nameRu} на выходные {year}
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

      <PriceCalendar
        data={monthlyPrices}
        destinationName={dest.nameRu}
        originName={originName(calendarOrigin)}
      />

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
