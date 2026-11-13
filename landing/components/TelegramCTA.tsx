import { SITE } from "../lib/site";
import type { Origin } from "../lib/destinations";

export function TelegramCTA({ origin }: { origin?: Origin }) {
  return (
    <section className="rounded-2xl bg-sky-500 px-6 py-10 text-center text-white">
      <h2 className="text-2xl font-bold">
        {origin
          ? `Билеты из ${origin.nameGen} — каждый день в Telegram`
          : "Лучшие билеты — каждый день в Telegram"}
      </h2>
      <p className="mx-auto mt-2 max-w-xl text-sky-50">
        {origin
          ? `Бот раз в день присылает самые дешёвые билеты на выходные из ${origin.nameGen}. Одно сообщение в день, только ваш город. Бесплатно.`
          : "Выберите свой город вылета в боте — раз в день он пришлёт самые дешёвые билеты на выходные. Одно сообщение в день, только ваш город. Бесплатно."}
      </p>
      <a
        href={origin ? `${SITE.bot}?start=${origin.slug}` : SITE.bot}
        target="_blank"
        rel="noopener"
        className="mt-6 inline-block rounded-full bg-white px-6 py-3 font-semibold text-sky-600 hover:bg-sky-50"
        data-umami-event="telegram-click"
        data-umami-event-source={origin ? `cta-${origin.slug}` : "cta"}
      >
        {origin ? "Подписаться в Telegram" : "Выбрать город в Telegram"}
      </a>
    </section>
  );
}
