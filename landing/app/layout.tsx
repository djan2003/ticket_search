import type { Metadata } from "next";
import Link from "next/link";
import "./globals.css";
import { SITE } from "../lib/site";
import { Analytics } from "../components/Analytics";

export const metadata: Metadata = {
  metadataBase: new URL(SITE.url),
  title: {
    default: SITE.title,
    template: `%s — ${SITE.name}`,
  },
  description: SITE.description,
  openGraph: {
    type: "website",
    locale: "ru_RU",
    siteName: SITE.name,
    title: SITE.title,
    description: SITE.description,
    url: SITE.url,
  },
};

export default function RootLayout({
  children,
}: Readonly<{ children: React.ReactNode }>) {
  return (
    <html lang="ru" className="h-full antialiased">
      <body className="min-h-full flex flex-col bg-slate-50 text-slate-900">
        <header className="border-b border-slate-200 bg-white">
          <div className="mx-auto flex max-w-5xl items-center justify-between px-4 py-4">
            <Link href="/" className="text-lg font-bold tracking-tight">
              ✈️ {SITE.name}
            </Link>
            <a
              href={SITE.telegram}
              target="_blank"
              rel="noopener"
              className="rounded-full bg-sky-500 px-4 py-2 text-sm font-semibold text-white hover:bg-sky-600"
              data-umami-event="telegram-click"
              data-umami-event-source="header"
            >
              Telegram-канал
            </a>
          </div>
        </header>

        <main className="flex-1">{children}</main>

        <footer className="border-t border-slate-200 bg-white">
          <div className="mx-auto max-w-5xl px-4 py-6 text-sm text-slate-500">
            <p>
              {SITE.name} — находим дешёвые авиабилеты на выходные по безвизовым
              направлениям из Батуми, Тбилиси и Еревана.
            </p>
            <p className="mt-1">
              Цены носят справочный характер и могут меняться. Покупка — на
              Aviasales.
            </p>
          </div>
        </footer>
        <Analytics />
      </body>
    </html>
  );
}
