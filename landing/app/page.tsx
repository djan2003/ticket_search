import Link from "next/link";
import { getFeed, allFlights, cheapestPerDestination } from "../lib/feed";
import { ORIGIN_LIST } from "../lib/destinations";
import { FlightTable } from "../components/FlightTable";
import { TelegramCTA } from "../components/TelegramCTA";
import { Faq } from "../components/Faq";

// Rendered on demand: reads the live feed each request (see lib/feed.ts).
export const dynamic = "force-dynamic";

const STEPS = [
  {
    title: "Ищем",
    text: "Бот каждый день опрашивает Aviasales по безвизовым направлениям из Батуми, Тбилиси и Еревана — на вылет в пятницу/субботу и возврат в воскресенье/понедельник.",
  },
  {
    title: "Фильтруем",
    text: "Оставляем только короткие поездки на выходные (до недели) и самые дешёвые варианты, без долгих пересадок.",
  },
  {
    title: "Публикуем",
    text: "Лучшие билеты попадают в Telegram-канал и в таблицу ниже. Покупка — на Aviasales в один клик.",
  },
];

const FAQ = [
  {
    q: "Откуда берутся цены?",
    a: "Из API Aviasales (Travelpayouts). Мы автоматически ищем дешёвые билеты раз в день и показываем лучшие найденные варианты.",
  },
  {
    q: "Это бесплатно?",
    a: "Да. Сайт и Telegram-канал бесплатные. Мы зарабатываем на партнёрской программе Aviasales, если вы покупаете билет по нашей ссылке — цена для вас при этом не меняется.",
  },
  {
    q: "Из каких городов ищете билеты?",
    a: "Сейчас это Батуми (BUS), Тбилиси (TBS) и Ереван (EVN). Список направлений — безвизовые страны.",
  },
  {
    q: "Почему только на выходные?",
    a: "Мы специально ищем короткие поездки: вылет в пятницу или субботу и возврат в воскресенье или понедельник, длительность до недели. Это удобно, чтобы слетать куда-то без отпуска.",
  },
  {
    q: "Как часто обновляются цены?",
    a: "Раз в день. Самые свежие предложения всегда появляются в Telegram-канале.",
  },
];

export default async function Home() {
  const feed = await getFeed();
  const top = cheapestPerDestination(allFlights(feed)).slice(0, 12);

  return (
    <div className="mx-auto max-w-5xl px-4 py-10 space-y-16">
      {/* Hero */}
      <section className="text-center">
        <h1 className="text-3xl font-extrabold tracking-tight sm:text-4xl">
          Дешёвые билеты на выходные из Батуми, Тбилиси и Еревана
        </h1>
        <p className="mx-auto mt-4 max-w-2xl text-lg text-slate-600">
          Находим самые низкие цены на короткие поездки по безвизовым
          направлениям: вылет в пятницу или субботу, возврат в воскресенье или
          понедельник. Автоматически, каждый день.
        </p>

        <div className="mt-6 flex flex-wrap items-center justify-center gap-2">
          <span className="text-sm font-medium text-slate-500">
            Откуда летим?
          </span>
          {ORIGIN_LIST.map((o) => (
            <Link
              key={o.slug}
              href={`/from/${o.slug}`}
              className="rounded-full bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700"
            >
              Из {o.nameRu}
            </Link>
          ))}
        </div>
      </section>

      {/* How it works */}
      <section>
        <h2 className="text-center text-2xl font-bold">Как это работает</h2>
        <div className="mt-8 grid gap-6 sm:grid-cols-3">
          {STEPS.map((s, i) => (
            <div
              key={i}
              className="rounded-xl border border-slate-200 bg-white p-6"
            >
              <div className="flex h-8 w-8 items-center justify-center rounded-full bg-sky-500 font-bold text-white">
                {i + 1}
              </div>
              <h3 className="mt-4 font-semibold">{s.title}</h3>
              <p className="mt-2 text-sm text-slate-600">{s.text}</p>
            </div>
          ))}
        </div>
      </section>

      {/* Top destinations */}
      <section>
        <h2 className="text-2xl font-bold">Сейчас дешевле всего на выходные</h2>
        <p className="mt-1 text-slate-600">
          Самые низкие цены по направлениям из найденных за последние сутки.
        </p>
        <div className="mt-6">
          <FlightTable flights={top} />
        </div>
      </section>

      <TelegramCTA />

      {/* FAQ */}
      <section>
        <h2 className="text-2xl font-bold">Частые вопросы</h2>
        <div className="mt-6">
          <Faq items={FAQ} />
        </div>
      </section>
    </div>
  );
}
