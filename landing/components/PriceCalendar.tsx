import type { MonthPrice } from "../lib/calendar";
import { formatPrice } from "../lib/format";

const MONTHS_RU = [
  "Янв", "Фев", "Мар", "Апр", "Май", "Июн",
  "Июл", "Авг", "Сен", "Окт", "Ноя", "Дек",
];

// Prepositional case for the caption ("дешевле всего в марте").
const MONTHS_RU_PREP = [
  "январе", "феврале", "марте", "апреле", "мае", "июне",
  "июле", "августе", "сентябре", "октябре", "ноябре", "декабре",
];

function monthIndex(ym: string): number {
  return Number(ym.split("-")[1]) - 1;
}

function monthLabel(ym: string): string {
  return MONTHS_RU[monthIndex(ym)] ?? ym;
}

function monthPrep(ym: string): string {
  return MONTHS_RU_PREP[monthIndex(ym)] ?? ym;
}

// Simple CSS bar chart of the cheapest price per month. The cheapest month is
// highlighted. Renders nothing when there's no data.
export function PriceCalendar({
  data,
  destinationName,
  originName,
}: {
  data: MonthPrice[];
  destinationName: string;
  originName: string;
}) {
  if (data.length === 0) return null;

  const max = Math.max(...data.map((d) => d.price));
  const cheapest = data.reduce((a, b) => (b.price < a.price ? b : a));

  return (
    <section>
      <h2 className="text-2xl font-bold">
        Когда дешевле лететь в {destinationName}
      </h2>
      <p className="mt-1 text-slate-600">
        Самая низкая цена авиабилета по месяцам из {originName}. Дешевле всего в{" "}
        <span className="font-semibold text-emerald-700">
          {monthPrep(cheapest.month)} — от {formatPrice(cheapest.price)}
        </span>
        .
      </p>

      <div className="mt-6 flex h-56 items-end gap-2 rounded-xl border border-slate-200 bg-white p-4">
        {data.map((d) => {
          const isCheapest = d.month === cheapest.month;
          return (
            <div
              key={d.month}
              className="flex flex-1 flex-col items-center justify-end gap-1"
            >
              <span className="text-xs font-medium text-slate-700">
                {formatPrice(d.price)}
              </span>
              <div
                className={`w-full rounded-t ${
                  isCheapest ? "bg-emerald-500" : "bg-sky-400"
                }`}
                style={{ height: `${Math.max((d.price / max) * 100, 6)}%` }}
                title={`${monthLabel(d.month)}: ${formatPrice(d.price)}`}
              />
              <span className="text-xs text-slate-500">
                {monthLabel(d.month)}
              </span>
            </div>
          );
        })}
      </div>
    </section>
  );
}
