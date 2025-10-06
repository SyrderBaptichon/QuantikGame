# Quantik Game - Docker Deployment

A web-based Quantik game built with PHP and PostgreSQL, containerized with Docker.

## Prerequisites

- Docker (version 20.10 or higher)
- Docker Compose (version 2.0 or higher)

## Quick Start

1. Clone the repository:
```bash
git clone <your-repo-url>
cd project-quantik
```

2. Configure environment variables (optional):
   Edit the `.env` file to customize database credentials if needed.

3. Start the application:
```bash
docker-compose up -d
```

4. Access the game:
- Web interface: http://localhost:8080
- Database: localhost:5433

## Configuration

### Environment Variables

Edit `.env` to customize:

```env
sgbd=pgsql
host=postgres
database=quantik
user=quantik_user
password=quantik_password
```

### Ports

- **8080**: Web application (Apache/PHP)
- **5433**: PostgreSQL database (external access)

To change ports, edit `docker-compose.yml`:
```yaml
ports:
  - "YOUR_PORT:80"  # Web
  - "YOUR_PORT:5432"  # Database
```

### Production Recommendations

1. **Change default passwords** in `.env`
2. **Use a reverse proxy** (nginx, Traefik) for HTTPS
3. **Set up backups** for PostgreSQL data
4. **Configure firewall** rules appropriately

## Troubleshooting

### Port already in use
If ports 8080 or 5433 are already in use, modify the `docker-compose.yml` file.

### Database connection errors
Ensure the database credentials in `db.php` match those in `.env`.

### View container logs
```bash
docker-compose logs quantik_php
docker-compose logs quantik_postgres
```

## Project Structure

```
project-quantik/
├── docker-compose.yml    # Docker orchestration
├── Dockerfile           # PHP container configuration
├── .env                 # Environment variables
├── db.php              # Database configuration
├── sql/
│   └── quantik.sql     # Database schema
└── ...                 # PHP application files
```

## License

[Your License Here]

## Support

For issues and questions, please open an issue on the repository.