export const SITE = {
  name: "Билеты на выходные",
  title: "Дешёвые авиабилеты на выходные без визы",
  description:
    "Автоматически находим дешёвые авиабилеты на выходные по безвизовым направлениям из Москвы, Санкт-Петербурга, Алматы, Астаны, Тбилиси, Батуми и Еревана: вылет в пятницу или субботу, возврат в воскресенье или понедельник. Лучшие цены по вашему городу — каждый день в Telegram.",
  url: process.env.NEXT_PUBLIC_SITE_URL ?? "https://flykend.com",
  telegram:
    process.env.NEXT_PUBLIC_TELEGRAM_URL ?? "https://t.me/cheap_flights_search",
  // Bot that sends the daily list for the city a person subscribed to.
  // "?start=<origin slug>" subscribes right away.
  bot: "https://t.me/tickets123_test777_search001_bot",
  // Umami analytics (self-hosted). Both must be set for tracking to render.
  // websiteId is created in the Umami dashboard after the first deploy.
  umamiSrc: process.env.NEXT_PUBLIC_UMAMI_SRC,
  umamiWebsiteId: process.env.NEXT_PUBLIC_UMAMI_WEBSITE_ID,
};
