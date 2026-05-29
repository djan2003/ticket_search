# --- build stage ---
FROM node:22-alpine AS builder
WORKDIR /app

COPY package.json package-lock.json ./
RUN npm ci

COPY . .

# NEXT_PUBLIC_* are inlined at build time, so they must be present here.
# API_FEED_URL is read at runtime (server-side) and is set via compose env.
ARG NEXT_PUBLIC_SITE_URL
ARG NEXT_PUBLIC_TELEGRAM_URL
ENV NEXT_PUBLIC_SITE_URL=$NEXT_PUBLIC_SITE_URL
ENV NEXT_PUBLIC_TELEGRAM_URL=$NEXT_PUBLIC_TELEGRAM_URL

RUN npm run build

# --- run stage ---
FROM node:22-alpine AS runner
WORKDIR /app

ENV NODE_ENV=production
ENV PORT=3000
ENV HOSTNAME=0.0.0.0

# Standalone output: minimal server + only the deps it needs.
COPY --from=builder /app/public ./public
COPY --from=builder /app/.next/standalone ./
COPY --from=builder /app/.next/static ./.next/static

EXPOSE 3000
CMD ["node", "server.js"]
