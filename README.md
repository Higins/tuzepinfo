# Tüzépinfo Ár Összesítő Rendszer

A Laravel alapú rendszer építőanyagok árainak gyűjtésére és kezelésére különböző forrásokból.

## Funkciók

- Több forrásból történő árgyűjtés (web scraping, API-k, adatbázisok)
- Ár előzmények tárolása forrás követéssel
- RESTful API az ár adatok eléréséhez
- Docker alapú fejlesztői környezet
- Bővíthető architektúra új adatforrások hozzáadásához

## Rendszerkövetelmények

- Docker és Docker Compose
- PHP 8.2+
- PostgreSQL
- Redis (cache)
- RabbitMQ

## Telepítés

1. Repository klónozása:
```bash
git clone <repository-url>
cd tuzepinfo
```

2. Környezeti fájl másolása:
```bash
cp .env.example .env
```

3. Docker konténerek indítása:
```bash
docker-compose up -d
```

4. Függőségek telepítése:
```bash
docker-compose exec app composer install
```

5. Alkalmazás kulcs generálása:
```bash
docker-compose exec app php artisan key:generate
```

6. Adatbázis migrációk futtatása:
```bash
docker-compose exec app php artisan migrate
```

7. Tesztadatok betöltése:
```bash
docker-compose exec app php artisan db:seed
```

### Seeder-ek Részletes Leírása

A rendszer a következő seeder-eket tartalmazza:

#### UserSeeder
- Admin felhasználó létrehozása:
  - Email: admin@tuzepinfo.hu
  - Jelszó: password
- 5 teszt felhasználó generálása Laravel Factory használatával

#### ProductSeeder
Alapvető építőanyagok létrehozása:
- Tégla (Falazó anyagok kategória)
- Cement (Kötőanyagok kategória)
- Homok (Összetevők kategória)
- Vasbeton (Vasbeton elemek kategória)
- Csempe (Burkolatok kategória)

#### PriceSourceSeeder
Árgyűjtő források létrehozása:
1. Tüzép.hu (Web Scraper)
   - CSS szelektorok konfigurációja
   - Aktív állapotban
2. Építőanyag API
   - API kulcs és végpont konfigurációja
   - Aktív állapotban
3. Építőanyagok.hu (Web Scraper)
   - CSS szelektorok konfigurációja
   - Aktív állapotban

#### PriceHistorySeeder
Ár előzmények generálása:
- Minden termékhez minden forrásból 5 ár előzmény
- Az utolsó 5 nap ár adatai
- Véletlenszerű árak 100-10000 HUF között
- HUF valuta használata

### Seeder-ek Egyedi Futtatása

Ha csak egy adott seeder-t szeretnél futtatni:

```bash
# Csak a felhasználók létrehozása
docker-compose exec app php artisan db:seed --class=UserSeeder

# Csak a termékek létrehozása
docker-compose exec app php artisan db:seed --class=ProductSeeder

# Csak az árgyűjtő források létrehozása
docker-compose exec app php artisan db:seed --class=PriceSourceSeeder

# Csak az ár előzmények létrehozása
docker-compose exec app php artisan db:seed --class=PriceHistorySeeder
```

### Seeder-ek Újrafuttatása

Ha tiszta adatbázisból szeretnéd újra futtatni a seeder-eket:

```bash
# Adatbázis törlése és újra létrehozása
docker-compose exec app php artisan migrate:fresh --seed
```

## Használat

### Árak Gyűjtése

Az összes aktív forrásból való árgyűjtéshez:

```bash
docker-compose exec app php artisan prices:collect
```

### API Végpontok

- `GET /api/v1/prices` - Összes termék listázása legfrissebb árakkal
- `GET /api/v1/prices/{product}` - Részletes ár előzmények lekérdezése egy adott termékhez

## Architektúra

A rendszer a következő tervezési mintákat és elveket használja:

- Strategy Pattern az árgyűjtőknél
- Repository Pattern az adathozzáféréshez
- SOLID elvek
- Laravel Service Container a dependency injection kezeléséhez

### Komponensek

- **Modellek**: Product, PriceSource, PriceHistory
- **Szolgáltatások**: PriceCollectorService, WebScraperCollector
- **Kontrollerek**: PriceController (API)
- **Parancsok**: CollectPrices

## Új Adatforrások Hozzáadása

Új adatforrás hozzáadásához:

1. Hozz létre egy új collector osztályt, ami implementálja a `PriceCollectorInterface`-t
2. Regisztrálj a collector-t a `CollectPrices` parancsban
3. Adja hozzá a forrás konfigurációt az adatbázishoz

Példa forrás konfigurációra:
```json
{
    "selector": ".product-item",
    "name_selector": ".product-name",
    "price_selector": ".product-price",
    "category_selector": ".product-category",
    "unit": "db"
}
```

## Fejlesztés

### Tesztek Futtatása

A projekt a következő típusú teszteket tartalmazza:

#### Unit Tesztek
- Modellek tesztelése (Product, PriceHistory, PriceSource)
- Kapcsolatok és metódusok tesztelése
- Adatbázis műveletek tesztelése

#### Feature Tesztek
- API végpontok tesztelése
- Integrációs tesztek
- Teljesítmény tesztek

Tesztek futtatása:
```bash
# Összes teszt futtatása
docker-compose exec app php artisan test

# Unit tesztek futtatása
docker-compose exec app php artisan test --filter=Unit

# Feature tesztek futtatása
docker-compose exec app php artisan test --filter=Feature

# Egyedi tesztfájl futtatása
docker-compose exec app php artisan test tests/Unit/Models/ProductTest.php
```

### CI/CD Pipeline

A projekt GitHub Actions alapú CI/CD pipeline-t használ, amely a következő feladatokat végzi:

#### Tesztelés
- PHPUnit tesztek futtatása
- PostgreSQL adatbázis használata
- Xdebug kódlefedettség mérés

#### Kód Stílus
- Laravel Pint kódstílus ellenőrzés
- PSR-12 szabványok validálása

#### Dokumentáció
- Swagger/OpenAPI dokumentáció generálása
- API végpontok dokumentálása

A pipeline automatikusan fut:
- Minden push eseménynél a main branch-re
- Minden pull request eseménynél a main branch-re

### Tesztelési Környezet

A teszteléshez külön környezeti változók használatosak:
- `.env.testing` fájl
- Külön PostgreSQL adatbázis
- In-memory cache és session kezelés

### Kód Stílus

A projekt a PSR-12 kódolási szabványokat követi. A kód stílus ellenőrzéséhez:

```bash
# Kód stílus ellenőrzése
docker-compose exec app ./vendor/bin/pint

# Kód stílus javítása
docker-compose exec app ./vendor/bin/pint --test
```

### Docker Környezet

A projekt a következő Docker szolgáltatásokat tartalmazza:

- **app**: Laravel alkalmazás (PHP 8.2)
  - Port: 9000 (PHP-FPM)
  - Elérés: http://localhost:8000
  - API dokumentáció: http://localhost:8000/api/documentation

- **nginx**: Web szerver
  - Port: 8000
  - SSL: 443 (ha konfigurálva)
  - Elérés: http://localhost:8000

- **db**: PostgreSQL adatbázis
  - Port: 5432
  - Felhasználónév: postgres
  - Jelszó: postgres
  - Adatbázis: tuzepinfo
  - Elérés: localhost:5432

- **redis**: Redis szerver (cache és üzenetskezelés)
  - Port: 6379
  - Elérés: localhost:6379

- **rabbitmq**: Üzenetkezelő rendszer
  - Port: 5672 (AMQP)
  - Port: 15672 (Management UI)
  - Felhasználónév: admin
  - Jelszó: admin
  - Management UI: http://localhost:15672

- **grafana**: Metrikák megjelenítése
  - Port: 3000
  - Felhasználónév: admin
  - Jelszó: admin
  - Elérés: http://localhost:3000

- **prometheus**: Metrikák gyűjtése
  - Port: 9090
  - Elérés: http://localhost:9090
  - Metrikák: http://localhost:9090/metrics

- **node-exporter**: Rendszer metrikák exportálása
  - Port: 9100
  - Metrikák: http://localhost:9100/metrics

### Szolgáltatások Elérési Útjai

#### RabbitMQ Vezérlőfelület
- Üzenetek: http://localhost:15672/#/queues
- Exchange-ek: http://localhost:15672/#/exchanges
- Felhasználók: http://localhost:15672/#/users
- VHost-ok: http://localhost:15672/#/vhosts

#### API Dokumentáció
- Swagger UI: http://localhost:8000/api/documentation
- OpenAPI Spec: http://localhost:8000/api/documentation.json

#### Laravel Eszközök
- Telescope: http://localhost:8000/telescope
- Horizon: http://localhost:8000/horizon

### Környezeti Változók

A `.env` fájlban konfigurálható beállítások:

- **APP_NAME**: Alkalmazás neve
- **APP_ENV**: Környezet (local, production)
- **APP_DEBUG**: Debug mód
- **APP_URL**: Alkalmazás URL
- **DB_CONNECTION**: Adatbázis típusa
- **DB_HOST**: Adatbázis host
- **DB_PORT**: Adatbázis port
- **DB_DATABASE**: Adatbázis neve
- **DB_USERNAME**: Adatbázis felhasználónév
- **DB_PASSWORD**: Adatbázis jelszó
- **REDIS_HOST**: Redis host
- **REDIS_PORT**: Redis port
- **REDIS_PASSWORD**: Redis jelszó

### Fejlesztői Eszközök

- **Laravel Telescope**: Debug és teljesítmény monitorozás
- **Laravel Horizon**: Redis üzenets monitorozás
- **Swagger/OpenAPI**: API dokumentáció

### Biztonság

- CSRF védelem
- XSS védelem
- SQL injection védelem
- Rate limiting

### Teljesítmény Optimalizálás

- Redis cache használata
- Adatbázis indexelés
- Query optimalizálás
- Asset minifikáció

### RabbitMQ Integráció

A rendszer RabbitMQ-t használ az aszinkron árgyűjtéshez és értesítések kezeléséhez. A következő komponensek tartoznak hozzá:

#### Konfiguráció
- `.env` fájlban:
  ```
  RABBITMQ_HOST=rabbitmq
  RABBITMQ_PORT=5672
  RABBITMQ_USER=admin
  RABBITMQ_PASSWORD=admin
  RABBITMQ_VHOST=/
  RABBITMQ_QUEUE=price_collection
  RABBITMQ_EXCHANGE=price_updates
  ```

#### Komponensek

1. **RabbitMQService**
   - Kapcsolat kezelése a RabbitMQ szerverrel
   - Üzenetek küldése és fogadása
   - Queue és exchange deklaráció
   - Hibakezelés és naplózás

2. **CollectPricesJob**
   - Aszinkron árgyűjtés végrehajtása
   - Sikeres/sikertelen végrehajtás értesítése
   - Termékek létrehozása/frissítése
   - Ár előzmények mentése

3. **ProcessPriceCollectionQueue**
   - Üzenetek feldolgozása a várólistából
   - Értesítések naplózása
   - Hibakezelés és visszajelzés

#### Használat

1. **Job-ok Futtatása**
   ```bash
   # Job-ok feldolgozása
   docker-compose exec app php artisan queue:process-price-collection
   ```

2. **Árak Gyűjtése**
   ```bash
   # Összes forrásból való árgyűjtés
   docker-compose exec app php artisan prices:collect
   ```

3. **Monitoring**
   - RabbitMQ Management UI: http://localhost:15672
   - Felhasználónév: admin
   - Jelszó: admin

#### Metrikák

A rendszer a következő metrikákat követi:
- Queue hossz
- Üzenetek sebessége
- Fogyasztók száma
- Memória használat
- Lemezterület használat

#### Hibakezelés

- Kapcsolat megszakadás esetén automatikus újracsatlakozás
- Üzenetek újrapróbálása hiba esetén
- Részletes naplózás minden műveletről
- Értesítések küldése sikertelen végrehajtás esetén

#### Biztonság

- SSL/TLS támogatás
- VHost izoláció
- Üzenetek perzisztálása