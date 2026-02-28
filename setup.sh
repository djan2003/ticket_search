#!/bin/bash

echo "🚀 Flight Search - Setup Script"
echo "================================"
echo ""

# Check if .env exists
if [ ! -f .env ]; then
    echo "📝 Creating .env file from .env.example..."
    cp .env.example .env
    echo "✓ .env file created"
    echo ""
    echo "⚠️  IMPORTANT: Edit .env file and add your API tokens:"
    echo "   - AVIASALES_API_TOKEN"
    echo "   - AVIASALES_MARKER"
    echo "   - TELEGRAM_BOT_TOKEN"
    echo "   - TELEGRAM_CHAT_ID"
    echo ""
else
    echo "✓ .env file already exists"
fi

# Make artisan executable
chmod +x artisan

echo "🐳 Building Docker containers..."
docker-compose build

echo ""
echo "🚀 Starting Docker containers..."
docker-compose up -d

echo ""
echo "⏳ Waiting for MySQL to be ready..."
sleep 10

echo ""
echo "📦 Running database migrations..."
docker-compose exec -T app php artisan migrate --force

echo ""
echo "✅ Setup complete!"
echo ""
echo "📋 Next steps:"
echo "   1. Edit .env file with your API tokens"
echo "   2. Restart containers: docker-compose restart"
echo "   3. Test the search: docker-compose exec app php artisan flights:search"
echo ""
echo "📖 View logs:"
echo "   docker-compose logs -f app"
echo ""
echo "🌐 API is available at: http://localhost"
