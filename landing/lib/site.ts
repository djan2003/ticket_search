export const SITE = {
  name: "Билеты на выходные",
  title: "Дешёвые билеты на выходные из Батуми, Тбилиси и Еревана",
  description:
    "Автоматически находим дешёвые авиабилеты на выходные по безвизовым направлениям из Батуми, Тбилиси и Еревана: вылет в пятницу или субботу, возврат в воскресенье или понедельник. Лучшие цены — каждый день в Telegram.",
  url: process.env.NEXT_PUBLIC_SITE_URL ?? "https://example.com",
  telegram:
    process.env.NEXT_PUBLIC_TELEGRAM_URL ?? "https://t.me/cheap_flights_search",
};
