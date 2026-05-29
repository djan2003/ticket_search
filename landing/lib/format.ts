export function formatPrice(price: number, currency = "USD"): string {
  const symbol = currency === "USD" ? "$" : currency;
  return `${Math.round(price)} ${symbol}`;
}

export function formatDate(iso: string | null): string {
  if (!iso) return "—";
  const d = new Date(iso);
  if (Number.isNaN(d.getTime())) return "—";
  return d.toLocaleDateString("ru-RU", {
    day: "2-digit",
    month: "2-digit",
    year: "numeric",
  });
}

export function stopsLabel(stops: number | null): string {
  if (stops === 0) return "Прямой";
  if (stops === 1) return "1 пересадка";
  if (stops && stops > 1) return `${stops} пересадки`;
  return "";
}
