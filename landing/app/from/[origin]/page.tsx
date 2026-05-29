import type { Metadata } from "next";
import Link from "next/link";
import { notFound } from "next/navigation";
import { ORIGIN_LIST, originBySlug } from "../../../lib/destinations";
import {
  getFeed,
  flightsFromOrigin,
  cheapestPerDestination,
} from "../../../lib/feed";
import { FlightTable } from "../../../components/FlightTable";
import { TelegramCTA } from "../../../components/TelegramCTA";
import { Faq } from "../../../components/Faq";
import { formatPrice } from "../../../lib/format";

type Params = { params: Promise<{ origin: string }> };

// Rendered on demand: reads the live feed each request (see lib/feed.ts).
export const dynamic = "force-dynamic";

export async function generateMetadata({ params }: Params): Promise<Metadata> {
  const { origin } = await params;
  const city = originBySlug(origin);
  if (!city) return {};

  const year = new Date().getFullYear();
  const title = `Дешёвые билеты из ${city.nameRu} ${year}`;
  const description = `Самые низкие цены на авиабилеты из ${city.nameRu} на выходные по безвизовым направлениям: вылет в пятницу/субботу, возврат в воскресенье/понедельник. Обновляется каждый день.`;

  return {
    title,
    description,
    alternates: { canonical: `/from/${city.slug}` },
    openGraph: { title, description },
  };
}

export default async function OriginPage({ params }: Params) {
  const { origin } = await params;
  const city = originBySlug(origin);
  if (!city) notFound();

  const feed = await getFeed();
  const flights = cheapestPerDestination(flightsFromOrigin(feed, city.iata));
  const cheapest = flights[0];
  const year = new Date().getFullYear();

  const otherOrigins = ORIGIN_LIST.filter((o) => o.slug !== city.slug);

  return (
    <div className="mx-auto max-w-5xl px-4 py-10 space-y-12">
      <nav className="text-sm text-slate-500">
        <Link href="/" className="hover:underline">
          Главная
        </Link>{" "}
        / Из {city.nameRu}
      </nav>

      <section>
        <h1 className="text-3xl font-extrabold tracking-tight">
          Билеты из {city.nameRu} на выходные {year}
        </h1>
        <p className="mt-4 max-w-2xl text-lg text-slate-600">
          Самые низкие цены на короткие поездки из {city.nameRu}: вылет в
          пятницу или субботу, возврат в воскресенье или понедельник. Только
          рейсы из вашего города — без лишнего. Обновляем каждый день.
        </p>
        {cheapest && (
          <p className="mt-4 inline-block rounded-lg bg-emerald-50 px-4 py-2 font-semibold text-emerald-700">
            Сейчас от {formatPrice(cheapest.price, cheapest.currency)}{" "}
            туда-обратно на выходные
          </p>
        )}

        <div className="mt-6 flex flex-wrap items-center gap-2 text-sm">
          <span className="text-slate-500">Другой город вылета:</span>
          {otherOrigins.map((o) => (
            <Link
              key={o.slug}
              href={`/from/${o.slug}`}
              className="rounded-full border border-slate-200 bg-white px-3 py-1.5 hover:border-sky-300 hover:text-sky-600"
            >
              {o.nameRu}
            </Link>
          ))}
        </div>
      </section>

      <section>
        <h2 className="text-2xl font-bold">
          Куда улететь из {city.nameRu} на выходные
        </h2>
        <p className="mt-1 text-slate-600">
          Самая низкая цена по каждому направлению.
        </p>
        <div className="mt-6">
          <FlightTable flights={flights} />
        </div>
      </section>

      <TelegramCTA />

      <section>
        <h2 className="text-2xl font-bold">Частые вопросы</h2>
        <div className="mt-6">
          <Faq
            items={[
              {
                q: `Откуда вылеты?`,
                a: `На этой странице — только рейсы из ${city.nameRu}. Билеты из других городов смотрите на отдельных страницах: ${otherOrigins
                  .map((o) => o.nameRu)
                  .join(", ")}.`,
              },
              {
                q: "Как часто обновляются цены?",
                a: "Раз в день. Самые свежие предложения всегда в Telegram-канале.",
              },
              {
                q: "Это бесплатно?",
                a: "Да. Покупка билета — на Aviasales, цена для вас не меняется.",
              },
            ]}
          />
        </div>
      </section>
    </div>
  );
}
