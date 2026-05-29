import Link from "next/link";
import type { Flight } from "../lib/types";
import {
  originName,
  destinationName,
  destinationByIata,
} from "../lib/destinations";
import { formatPrice, formatDate, stopsLabel } from "../lib/format";

export function FlightTable({
  flights,
  linkDestination = true,
}: {
  flights: Flight[];
  linkDestination?: boolean;
}) {
  if (flights.length === 0) {
    return (
      <p className="rounded-xl border border-dashed border-slate-300 p-6 text-center text-slate-500">
        Цены обновляются. Загляните в Telegram-канал — там свежие билеты каждый
        день.
      </p>
    );
  }

  return (
    <div className="overflow-x-auto rounded-xl border border-slate-200 bg-white">
      <table className="w-full text-sm">
        <thead className="bg-slate-50 text-left text-slate-500">
          <tr>
            <th className="px-4 py-3 font-medium">Маршрут</th>
            <th className="px-4 py-3 font-medium">Даты</th>
            <th className="px-4 py-3 font-medium">Пересадки</th>
            <th className="px-4 py-3 font-medium">Цена</th>
            <th className="px-4 py-3" />
          </tr>
        </thead>
        <tbody className="divide-y divide-slate-100">
          {flights.map((f, i) => {
            const dest = destinationByIata(f.destination);
            return (
              <tr key={i} className="hover:bg-slate-50">
                <td className="px-4 py-3 font-medium">
                  {originName(f.origin)} →{" "}
                  {linkDestination && dest ? (
                    <Link
                      href={`/${dest.slug}`}
                      className="text-sky-600 hover:underline"
                    >
                      {destinationName(f.destination)}
                    </Link>
                  ) : (
                    destinationName(f.destination)
                  )}
                </td>
                <td className="whitespace-nowrap px-4 py-3 text-slate-600">
                  {formatDate(f.departure_date)} — {formatDate(f.return_date)}
                </td>
                <td className="px-4 py-3 text-slate-600">
                  {stopsLabel(f.stops)}
                </td>
                <td className="whitespace-nowrap px-4 py-3 font-bold">
                  {formatPrice(f.price, f.currency)}
                </td>
                <td className="px-4 py-3 text-right">
                  <a
                    href={f.link}
                    target="_blank"
                    rel="nofollow noopener sponsored"
                    className="inline-block rounded-lg bg-sky-500 px-3 py-1.5 text-xs font-semibold text-white hover:bg-sky-600"
                  >
                    Купить
                  </a>
                </td>
              </tr>
            );
          })}
        </tbody>
      </table>
    </div>
  );
}
