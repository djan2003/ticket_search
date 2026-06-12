import { SITE } from "./site";
import { formatPrice } from "./format";

/** Site-wide Organization + WebSite schema (brand recognition, sitelinks). */
export function siteJsonLd() {
  return {
    "@context": "https://schema.org",
    "@graph": [
      {
        "@type": "Organization",
        "@id": `${SITE.url}/#organization`,
        name: SITE.name,
        url: SITE.url,
        sameAs: [SITE.telegram],
      },
      {
        "@type": "WebSite",
        "@id": `${SITE.url}/#website`,
        name: SITE.name,
        url: SITE.url,
        inLanguage: "ru-RU",
        publisher: { "@id": `${SITE.url}/#organization` },
      },
    ],
  };
}

/** FAQPage schema for rich results (questions shown directly in search). */
export function faqJsonLd(items: { q: string; a: string }[]) {
  return {
    "@context": "https://schema.org",
    "@type": "FAQPage",
    mainEntity: items.map((it) => ({
      "@type": "Question",
      name: it.q,
      acceptedAnswer: { "@type": "Answer", text: it.a },
    })),
  };
}

/** BreadcrumbList schema so search shows the URL as a breadcrumb trail. */
export function breadcrumbJsonLd(items: { name: string; path: string }[]) {
  return {
    "@context": "https://schema.org",
    "@type": "BreadcrumbList",
    itemListElement: items.map((it, i) => ({
      "@type": "ListItem",
      position: i + 1,
      name: it.name,
      item: `${SITE.url}${it.path}`,
    })),
  };
}

/**
 * Meta description enriched with the live minimum price. Falls back to a
 * price-free description when the feed is empty so the page is never broken.
 */
export function priceMetaDescription(
  lead: string,
  minPrice: number | null,
  currency: string,
): string {
  const tail =
    minPrice != null
      ? ` от ${formatPrice(minPrice, currency)}. Сравнивайте цены на прямые рейсы и перелёты с пересадками — обновляем каждый день.`
      : ". Актуальные цены на прямые рейсы и перелёты с пересадками — обновляем каждый день.";
  return `✈️ ${lead}${tail}`;
}
