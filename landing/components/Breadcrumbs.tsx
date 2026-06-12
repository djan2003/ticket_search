import Link from "next/link";
import { JsonLd } from "./JsonLd";
import { breadcrumbJsonLd } from "../lib/seo";

export type Crumb = { name: string; path: string };

// Visual breadcrumb trail + matching BreadcrumbList JSON-LD. The last crumb
// is the current page (not a link).
export function Breadcrumbs({ items }: { items: Crumb[] }) {
  return (
    <>
      <JsonLd data={breadcrumbJsonLd(items)} />
      <nav className="text-sm text-slate-500">
        {items.map((it, i) => {
          const last = i === items.length - 1;
          return (
            <span key={it.path}>
              {i > 0 && " / "}
              {last ? (
                it.name
              ) : (
                <Link href={it.path} className="hover:underline">
                  {it.name}
                </Link>
              )}
            </span>
          );
        })}
      </nav>
    </>
  );
}
