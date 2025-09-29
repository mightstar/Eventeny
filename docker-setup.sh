#!/bin/bash

# Eventeny Docker Setup Script
echo "🚀 Setting up Eventeny with Docker..."

# Check if Docker is installed
if ! command -v docker &> /dev/null; then
    echo "❌ Docker is not installed. Please install Docker first."
    exit 1
fi

# Check if Docker Compose is installed
if ! command -v docker compose &> /dev/null; then
    echo "❌ Docker Compose is not installed. Please install Docker Compose first."
    exit 1
fi

# Create uploads directory if it doesn't exist
echo "📁 Creating uploads directory..."
mkdir -p uploads
chmod 755 uploads

# Build and start containers
echo "🔨 Building Docker containers..."
docker compose build

echo "🚀 Starting containers..."
docker compose up -d

# Wait for MySQL to be ready
echo "⏳ Waiting for MySQL to be ready..."
sleep 10

# Check if containers are running
echo "📊 Container status:"
docker compose ps

echo ""
echo "✅ Eventeny is now running!"
echo ""
echo "🌐 Application: http://localhost:8080"
echo "🗄️  phpMyAdmin: http://localhost:8081"
echo ""
echo "🛠️  Commands:"
echo "   Stop: docker compose down"
echo "   Logs: docker compose logs -f"
echo "   Rebuild: docker compose up --build"
