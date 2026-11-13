// Departure cities (mirrors config/airports.php on the backend)
export const ORIGINS: Record<string, string> = {
  BUS: "Батуми",
  TBS: "Тбилиси",
  EVN: "Ереван",
  ALA: "Алматы",
  NQZ: "Астана",
  MOW: "Москва",
  LED: "Санкт-Петербург",
};

// Passport whose visa-free list is searched (mirrors config/countries.php)
export type Passport = "RU" | "KZ";

// Genitive, for "паспорт России"
export const PASSPORT_NAMES: Record<Passport, string> = {
  RU: "России",
  KZ: "Казахстана",
};

export type Destination = {
  iata: string;
  slug: string;
  nameRu: string;
  countryRu: string;
  blurb: string;
  // Passports this destination is visa-free for
  passports: Passport[];
};

// Visa-free destinations per passport (mirrors config/countries.php on the backend)
export const DESTINATIONS: Destination[] = [
  { iata: "EVN", slug: "yerevan", nameRu: "Ереван", countryRu: "Армения", blurb: "Древняя столица у подножия Арарата, коньяк и тёплый приём.", passports: ["RU", "KZ"] },
  { iata: "MSQ", slug: "minsk", nameRu: "Минск", countryRu: "Беларусь", blurb: "Просторный и зелёный город с безвизовым въездом.", passports: ["RU", "KZ"] },
  { iata: "ALA", slug: "almaty", nameRu: "Алматы", countryRu: "Казахстан", blurb: "Горы, фуникулёры и лучшая еда Центральной Азии.", passports: ["RU"] },
  { iata: "NQZ", slug: "astana", nameRu: "Астана", countryRu: "Казахстан", blurb: "Футуристичная столица в степи.", passports: ["RU"] },
  { iata: "GUW", slug: "atyrau", nameRu: "Атырау", countryRu: "Казахстан", blurb: "Город на Урале, ворота к Каспию.", passports: ["RU"] },
  { iata: "BSZ", slug: "bishkek", nameRu: "Бишкек", countryRu: "Киргизия", blurb: "Старт для треков по Тянь-Шаню и озеру Иссык-Куль.", passports: ["RU", "KZ"] },
  { iata: "OSS", slug: "osh", nameRu: "Ош", countryRu: "Киргизия", blurb: "Один из древнейших городов Ферганской долины.", passports: ["RU"] },
  { iata: "DYU", slug: "dushanbe", nameRu: "Душанбе", countryRu: "Таджикистан", blurb: "Зелёная столица у Памирских гор.", passports: ["RU", "KZ"] },
  { iata: "TAS", slug: "tashkent", nameRu: "Ташкент", countryRu: "Узбекистан", blurb: "Метро-музей, базары и плов.", passports: ["RU", "KZ"] },
  { iata: "SKD", slug: "samarkand", nameRu: "Самарканд", countryRu: "Узбекистан", blurb: "Регистан и бирюзовые купола Великого шёлкового пути.", passports: ["RU", "KZ"] },
  { iata: "BEG", slug: "belgrade", nameRu: "Белград", countryRu: "Сербия", blurb: "Крепость Калемегдан и бурная ночная жизнь на Дунае.", passports: ["RU", "KZ"] },
  { iata: "IST", slug: "istanbul", nameRu: "Стамбул", countryRu: "Турция", blurb: "Город на двух континентах: Босфор, базары и кухня.", passports: ["RU", "KZ"] },
  { iata: "SAW", slug: "istanbul-sabiha", nameRu: "Стамбул (Сабиха)", countryRu: "Турция", blurb: "Азиатский аэропорт Стамбула — часто дешевле.", passports: ["RU", "KZ"] },
  { iata: "AYT", slug: "antalya", nameRu: "Анталья", countryRu: "Турция", blurb: "Пляжи Средиземноморья и Ликийская тропа.", passports: ["RU", "KZ"] },
  { iata: "ADB", slug: "izmir", nameRu: "Измир", countryRu: "Турция", blurb: "Эгейское побережье и древний Эфес неподалёку.", passports: ["RU"] },
  { iata: "BKK", slug: "bangkok", nameRu: "Бангкок", countryRu: "Таиланд", blurb: "Храмы, уличная еда и ворота в Юго-Восточную Азию.", passports: ["RU", "KZ"] },
  { iata: "HKT", slug: "phuket", nameRu: "Пхукет", countryRu: "Таиланд", blurb: "Главный пляжный остров Андаманского моря.", passports: ["RU"] },
  { iata: "CNX", slug: "chiang-mai", nameRu: "Чиангмай", countryRu: "Таиланд", blurb: "Горный север Таиланда, храмы и кофейни.", passports: ["RU"] },
  { iata: "MLE", slug: "male", nameRu: "Мале", countryRu: "Мальдивы", blurb: "Атоллы, бунгало на воде и дайвинг.", passports: ["RU", "KZ"] },
  { iata: "DXB", slug: "dubai", nameRu: "Дубай", countryRu: "ОАЭ", blurb: "Небоскрёбы, шопинг и пляжи круглый год.", passports: ["RU", "KZ"] },
  { iata: "AUH", slug: "abu-dhabi", nameRu: "Абу-Даби", countryRu: "ОАЭ", blurb: "Мечеть шейха Зайда и остров Яс.", passports: ["RU", "KZ"] },
  { iata: "CMN", slug: "casablanca", nameRu: "Касабланка", countryRu: "Марокко", blurb: "Крупнейший город Марокко и мечеть Хасана II.", passports: ["RU"] },
  { iata: "RAK", slug: "marrakesh", nameRu: "Марракеш", countryRu: "Марокко", blurb: "Медина, площадь Джемаа эль-Фна и Атлас рядом.", passports: ["RU"] },
  { iata: "TUN", slug: "tunis", nameRu: "Тунис", countryRu: "Тунис", blurb: "Карфаген, Сиди-Бу-Саид и Средиземное море.", passports: ["RU"] },
  { iata: "HAV", slug: "havana", nameRu: "Гавана", countryRu: "Куба", blurb: "Ретро-автомобили, ром и карибские ритмы.", passports: ["RU"] },
  { iata: "HAN", slug: "hanoi", nameRu: "Ханой", countryRu: "Вьетнам", blurb: "Старый квартал, фо и бухта Халонг неподалёку.", passports: ["RU", "KZ"] },
  { iata: "SGN", slug: "ho-chi-minh", nameRu: "Хошимин", countryRu: "Вьетнам", blurb: "Энергичный юг Вьетнама и дельта Меконга.", passports: ["RU"] },
  { iata: "SHJ", slug: "sharjah", nameRu: "Шарджа", countryRu: "ОАЭ", blurb: "Соседний с Дубаем эмират — часто дешевле долететь.", passports: ["KZ"] },
  { iata: "TBS", slug: "tbilisi", nameRu: "Тбилиси", countryRu: "Грузия", blurb: "Старый город, серные бани и грузинская кухня.", passports: ["RU", "KZ"] },
  { iata: "KUT", slug: "kutaisi", nameRu: "Кутаиси", countryRu: "Грузия", blurb: "Лоукост-ворота Грузии рядом с каньонами и пещерами.", passports: ["RU", "KZ"] },
  { iata: "GYD", slug: "baku", nameRu: "Баку", countryRu: "Азербайджан", blurb: "Каспийская набережная, Старый город и Пламенные башни.", passports: ["RU", "KZ"] },
  { iata: "MOW", slug: "moscow", nameRu: "Москва", countryRu: "Россия", blurb: "Музеи, театры и большой город на выходные.", passports: ["KZ"] },
  { iata: "LED", slug: "saint-petersburg", nameRu: "Санкт-Петербург", countryRu: "Россия", blurb: "Эрмитаж, каналы и разводные мосты.", passports: ["KZ"] },
  { iata: "URC", slug: "urumqi", nameRu: "Урумчи", countryRu: "Китай", blurb: "Ближайший к Алматы китайский мегаполис: базары и уйгурская кухня.", passports: ["KZ"] },
  { iata: "BJS", slug: "beijing", nameRu: "Пекин", countryRu: "Китай", blurb: "Запретный город и Великая стена неподалёку.", passports: ["KZ"] },
  { iata: "HKG", slug: "hong-kong", nameRu: "Гонконг", countryRu: "Гонконг", blurb: "Небоскрёбы, пик Виктория и уличная еда.", passports: ["RU", "KZ"] },
  { iata: "UBN", slug: "ulaanbaatar", nameRu: "Улан-Батор", countryRu: "Монголия", blurb: "Столица степей и старт для поездок в Гоби.", passports: ["KZ"] },
  { iata: "DOH", slug: "doha", nameRu: "Доха", countryRu: "Катар", blurb: "Музей исламского искусства, сук Вакиф и набережная Корниш.", passports: ["RU", "KZ"] },
  { iata: "MCT", slug: "muscat", nameRu: "Маскат", countryRu: "Оман", blurb: "Белый город между горами и Оманским заливом.", passports: ["RU", "KZ"] },
  { iata: "CXR", slug: "nha-trang", nameRu: "Нячанг", countryRu: "Вьетнам", blurb: "Пляжный курорт Вьетнама с островами и дайвингом.", passports: ["KZ"] },
  { iata: "KUL", slug: "kuala-lumpur", nameRu: "Куала-Лумпур", countryRu: "Малайзия", blurb: "Башни Петронас, пещеры Бату и азиатская кухня.", passports: ["RU", "KZ"] },
  { iata: "AMM", slug: "amman", nameRu: "Амман", countryRu: "Иордания", blurb: "Ворота к Петре, Мёртвому морю и пустыне Вади-Рам.", passports: ["RU"] },
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

export type Origin = {
  iata: string;
  slug: string;
  nameRu: string;
  // Genitive, for lists like "из Батуми, Тбилиси и Еревана"
  nameGen: string;
  // Visa-free list searched from this city (mirrors config/airports.php)
  passport: Passport;
};

export const ORIGIN_LIST: Origin[] = [
  { iata: "BUS", slug: "batumi", nameRu: "Батуми", nameGen: "Батуми", passport: "RU" },
  { iata: "TBS", slug: "tbilisi", nameRu: "Тбилиси", nameGen: "Тбилиси", passport: "RU" },
  { iata: "EVN", slug: "yerevan", nameRu: "Ереван", nameGen: "Еревана", passport: "RU" },
  { iata: "ALA", slug: "almaty", nameRu: "Алматы", nameGen: "Алматы", passport: "KZ" },
  { iata: "NQZ", slug: "astana", nameRu: "Астана", nameGen: "Астаны", passport: "KZ" },
  { iata: "MOW", slug: "moscow", nameRu: "Москва", nameGen: "Москвы", passport: "RU" },
  { iata: "LED", slug: "saint-petersburg", nameRu: "Санкт-Петербург", nameGen: "Санкт-Петербурга", passport: "RU" },
];

/** Origin cities that have flights searched to this destination. */
export function originsForDestination(dest: Destination): Origin[] {
  return ORIGIN_LIST.filter(
    (o) => o.iata !== dest.iata && dest.passports.includes(o.passport),
  );
}

/** "Батуми, Тбилиси и Еревана" */
export function joinRu(names: string[]): string {
  if (names.length <= 1) return names.join("");
  return `${names.slice(0, -1).join(", ")} и ${names.at(-1)}`;
}

const ORIGIN_BY_SLUG = new Map(ORIGIN_LIST.map((o) => [o.slug, o]));

export function originBySlug(slug: string): Origin | undefined {
  return ORIGIN_BY_SLUG.get(slug);
}
