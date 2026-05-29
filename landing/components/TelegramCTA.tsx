import { SITE } from "../lib/site";

export function TelegramCTA() {
  return (
    <section className="rounded-2xl bg-sky-500 px-6 py-10 text-center text-white">
      <h2 className="text-2xl font-bold">
        Лучшие билеты — каждый день в Telegram
      </h2>
      <p className="mx-auto mt-2 max-w-xl text-sky-50">
        Подпишитесь на канал: бот публикует самые дешёвые билеты из Батуми,
        Тбилиси и Еревана раз в день. Бесплатно.
      </p>
      <a
        href={SITE.telegram}
        target="_blank"
        rel="noopener"
        className="mt-6 inline-block rounded-full bg-white px-6 py-3 font-semibold text-sky-600 hover:bg-sky-50"
      >
        Подписаться на канал
      </a>
    </section>
  );
}
