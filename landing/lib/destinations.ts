// Departure cities (mirrors config/airports.php on the backend)
export const ORIGINS: Record<string, string> = {
  BUS: "Батуми",
  TBS: "Тбилиси",
  EVN: "Ереван",
};

export type Destination = {
  iata: string;
  slug: string;
  nameRu: string;
  countryRu: string;
  blurb: string;
};

// Visa-free destinations (mirrors config/countries.php on the backend)
export const DESTINATIONS: Destination[] = [
  { iata: "EVN", slug: "yerevan", nameRu: "Ереван", countryRu: "Армения", blurb: "Древняя столица у подножия Арарата, коньяк и тёплый приём." },
  { iata: "MSQ", slug: "minsk", nameRu: "Минск", countryRu: "Беларусь", blurb: "Просторный и зелёный город с безвизовым въездом." },
  { iata: "ALA", slug: "almaty", nameRu: "Алматы", countryRu: "Казахстан", blurb: "Горы, фуникулёры и лучшая еда Центральной Азии." },
  { iata: "TSE", slug: "astana", nameRu: "Астана", countryRu: "Казахстан", blurb: "Футуристичная столица в степи." },
  { iata: "GUW", slug: "atyrau", nameRu: "Атырау", countryRu: "Казахстан", blurb: "Город на Урале, ворота к Каспию." },
  { iata: "FRU", slug: "bishkek", nameRu: "Бишкек", countryRu: "Киргизия", blurb: "Старт для треков по Тянь-Шаню и озеру Иссык-Куль." },
  { iata: "OSS", slug: "osh", nameRu: "Ош", countryRu: "Киргизия", blurb: "Один из древнейших городов Ферганской долины." },
  { iata: "KIV", slug: "chisinau", nameRu: "Кишинёв", countryRu: "Молдова", blurb: "Винные погреба Криково и Милешты-Мичь рядом." },
  { iata: "DYU", slug: "dushanbe", nameRu: "Душанбе", countryRu: "Таджикистан", blurb: "Зелёная столица у Памирских гор." },
  { iata: "TAS", slug: "tashkent", nameRu: "Ташкент", countryRu: "Узбекистан", blurb: "Метро-музей, базары и плов." },
  { iata: "SKD", slug: "samarkand", nameRu: "Самарканд", countryRu: "Узбекистан", blurb: "Регистан и бирюзовые купола Великого шёлкового пути." },
  { iata: "BEG", slug: "belgrade", nameRu: "Белград", countryRu: "Сербия", blurb: "Крепость Калемегдан и бурная ночная жизнь на Дунае." },
  { iata: "IST", slug: "istanbul", nameRu: "Стамбул", countryRu: "Турция", blurb: "Город на двух континентах: Босфор, базары и кухня." },
  { iata: "SAW", slug: "istanbul-sabiha", nameRu: "Стамбул (Сабиха)", countryRu: "Турция", blurb: "Азиатский аэропорт Стамбула — часто дешевле." },
  { iata: "AYT", slug: "antalya", nameRu: "Анталья", countryRu: "Турция", blurb: "Пляжи Средиземноморья и Ликийская тропа." },
  { iata: "ADB", slug: "izmir", nameRu: "Измир", countryRu: "Турция", blurb: "Эгейское побережье и древний Эфес неподалёку." },
  { iata: "BKK", slug: "bangkok", nameRu: "Бангкок", countryRu: "Таиланд", blurb: "Храмы, уличная еда и ворота в Юго-Восточную Азию." },
  { iata: "HKT", slug: "phuket", nameRu: "Пхукет", countryRu: "Таиланд", blurb: "Главный пляжный остров Андаманского моря." },
  { iata: "CNX", slug: "chiang-mai", nameRu: "Чиангмай", countryRu: "Таиланд", blurb: "Горный север Таиланда, храмы и кофейни." },
  { iata: "MLE", slug: "male", nameRu: "Мале", countryRu: "Мальдивы", blurb: "Атоллы, бунгало на воде и дайвинг." },
  { iata: "DXB", slug: "dubai", nameRu: "Дубай", countryRu: "ОАЭ", blurb: "Небоскрёбы, шопинг и пляжи круглый год." },
  { iata: "AUH", slug: "abu-dhabi", nameRu: "Абу-Даби", countryRu: "ОАЭ", blurb: "Мечеть шейха Зайда и остров Яс." },
  { iata: "TLV", slug: "tel-aviv", nameRu: "Тель-Авив", countryRu: "Израиль", blurb: "Средиземное море, баухаус и ночная жизнь." },
  { iata: "CMN", slug: "casablanca", nameRu: "Касабланка", countryRu: "Марокко", blurb: "Крупнейший город Марокко и мечеть Хасана II." },
  { iata: "RAK", slug: "marrakesh", nameRu: "Марракеш", countryRu: "Марокко", blurb: "Медина, площадь Джемаа эль-Фна и Атлас рядом." },
  { iata: "TUN", slug: "tunis", nameRu: "Тунис", countryRu: "Тунис", blurb: "Карфаген, Сиди-Бу-Саид и Средиземное море." },
  { iata: "HAV", slug: "havana", nameRu: "Гавана", countryRu: "Куба", blurb: "Ретро-автомобили, ром и карибские ритмы." },
  { iata: "HAN", slug: "hanoi", nameRu: "Ханой", countryRu: "Вьетнам", blurb: "Старый квартал, фо и бухта Халонг неподалёку." },
  { iata: "SGN", slug: "ho-chi-minh", nameRu: "Хошимин", countryRu: "Вьетнам", blurb: "Энергичный юг Вьетнама и дельта Меконга." },
  { iata: "CGK", slug: "jakarta", nameRu: "Джакарта", countryRu: "Индонезия", blurb: "Мегаполис-ворота в Индонезию." },
  { iata: "DPS", slug: "bali", nameRu: "Бали (Денпасар)", countryRu: "Индонезия", blurb: "Рисовые террасы, серф и храмы острова богов." },
];

const BY_SLUG = new Map(DESTINATIONS.map((d) => [d.slug, d]));
const BY_IATA = new Map(DESTINATIONS.map((d) => [d.iata, d]));

export function destinationBySlug(slug: string): Destination | undefined {
  return BY_SLUG.get(slug);
}

export function destinationByIata(iata: string): Destination | undefined {
  return BY_IATA.get(iata);
}

export function originName(iata: string): string {
  return ORIGINS[iata] ?? iata;
}

export function destinationName(iata: string): string {
  return BY_IATA.get(iata)?.nameRu ?? iata;
}

export type Origin = { iata: string; slug: string; nameRu: string };

export const ORIGIN_LIST: Origin[] = [
  { iata: "BUS", slug: "batumi", nameRu: "Батуми" },
  { iata: "TBS", slug: "tbilisi", nameRu: "Тбилиси" },
  { iata: "EVN", slug: "yerevan", nameRu: "Ереван" },
];

const ORIGIN_BY_SLUG = new Map(ORIGIN_LIST.map((o) => [o.slug, o]));

export function originBySlug(slug: string): Origin | undefined {
  return ORIGIN_BY_SLUG.get(slug);
}
