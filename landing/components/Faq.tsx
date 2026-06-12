import { JsonLd } from "./JsonLd";
import { faqJsonLd } from "../lib/seo";

export type FaqItem = { q: string; a: string };

export function Faq({ items }: { items: FaqItem[] }) {
  return (
    <>
      <JsonLd data={faqJsonLd(items)} />
      <div className="divide-y divide-slate-200 rounded-xl border border-slate-200 bg-white">
        {items.map((it, i) => (
          <details key={i} className="group p-4">
            <summary className="cursor-pointer font-medium">{it.q}</summary>
            <p className="mt-2 text-slate-600">{it.a}</p>
          </details>
        ))}
      </div>
    </>
  );
}
