export type MonthPrice = { month: string; price: number };

// The calendar endpoint lives next to the feed on the Laravel API. We derive
// its base from API_FEED_URL (e.g. http://web/api/feed -> http://web/api) so
// there's no extra env var to configure.
function apiBase(): string | null {
  const feedUrl = process.env.API_FEED_URL;
  if (!feedUrl) return null;
  return feedUrl.replace(/\/feed\/?$/, "");
}

/**
 * Cheapest price per month for a route. Never throws: returns [] on any
 * failure so the page still renders without the chart.
 */
export async function getMonthlyPrices(
  origin: string,
  destination: string,
): Promise<MonthPrice[]> {
  const base = apiBase();
  if (!base) return [];

  try {
    const res = await fetch(`${base}/calendar/${origin}/${destination}`, {
      cache: "no-store",
    });
    if (!res.ok) return [];
    const json = await res.json();
    return (json?.months ?? []) as MonthPrice[];
  } catch {
    return [];
  }
}
