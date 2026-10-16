<div align="center">

# 🏥 Nurse Simulator

[![Symfony](https://img.shields.io/badge/Symfony-7.4-000000?style=for-the-badge&logo=symfony&logoColor=white)](https://symfony.com/)
[![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://www.php.net/)
[![STUCOM](https://img.shields.io/badge/STUCOM-Pelai%20(Barcelona)-E2001A?style=for-the-badge)](https://www.stucom.com/)
[![Course](https://img.shields.io/badge/DAW%202-2026%2F27-4B0082?style=for-the-badge)](#)
[![License](https://img.shields.io/badge/License-MIT-blue.style=for-the-badge)](LICENSE)

<p align="center">
  <strong>API REST de Simulación y Gestión Hospitalaria de Personal de Enfermería</strong><br>
  M0616 — Proyecto Intermodular | 2º Desarrollo de Aplicaciones Web (DAW)
</p>

[🇪🇸 Documentación Completa (ES)](docs/general/DOCUMENTACION_ES.md) • [🇬🇧 Full Documentation (EN)](docs/general/DOCUMENTATION_EN.md) • [Colección Postman](docs/postman/nurse-api.postman_collection.json)

---

</div>

## 📖 1. Sobre el Proyecto

**Nurse Simulator** es una API REST desarrollada con **Symfony 7.4** y **PHP 8.2+** orientada a simular la administración, consulta e identificación de los enfermeros de un complejo hospitalario.

Este proyecto forma parte de la fase inicial (**pr03 — Introducción Backend**) del **Proyecto Intermodular (M0616)** cursado en **2º DAW** en el centro **STUCOM Pelai (Barcelona)** durante el curso académico **2026/2027**.

### 👥 Equipo de Desarrollo y Roles (Grupo pr03)

| Miembro | Rol y Responsabilidades | GitHub |
|---|---|---|
| **Ignacio Breñas** | **Project Manager & Tech Lead**<br>• Dirección y organización integral del equipo de desarrollo.<br>• Creación y estructuración de las ramas de trabajo individuales de cada miembro.<br>• Definición, reparto y seguimiento de Issues en el tablero de GitHub Projects.<br>• Supervisión de calidad, revisión e integración de Pull Requests asegurando el correcto funcionamiento global.<br>• Inicialización del framework Symfony 7.4 y centralización de la suite de pruebas en Postman. | [@Ignaciobrenas](https://github.com/Ignaciobrenas) |
| **Christopher Roger Tito** | **Desarrollador Backend**<br>• Implementación del endpoint de lectura general de enfermeros (`getAll`).<br>• Pruebas y evidencias en Postman para el listado global. | [@RogerTito455](https://github.com/RogerTito455) |
| **Zehao Yin** | **Desarrollador Backend**<br>• Implementación del endpoint de búsqueda por nombre (`findByName`).<br>• Ampliación del dataset de pruebas a 1.000 enfermeros en JSON. | [@zyin-08](https://github.com/zyin-08) |
| **Gorka Ramirez** | **Desarrollador Backend**<br>• Implementación del endpoint de validación y login de enfermeros (`login`).<br>• Pruebas unitarias de credenciales y evidencias en Postman. | [@gorkaramirez936](https://github.com/gorkaramirez936) |

---

## 🎯 2. Objetivos de la Práctica (STUCOM pr03)

Siguiendo las especificaciones del documento oficial docente (`pr03_Introduccio_back_end`):
1. **Aprendizaje y bases del trabajo grupal con Symfony:** Creación de proyecto modular e integración progresiva de ramas de trabajo en equipo.
2. **Desarrollo de API REST estática:** Exposición de endpoints HTTP que devuelven respuestas en formato JSON a partir de datos estructurados en archivos (simulando persistencia previa a la introducción de bases de datos relacionales).
3. **Flujo de trabajo individual a grupal:**
   - Desarrollo individual de endpoints especializados por cada miembro.
   - Fusión e integración coordinada en un repositorio común (`group repo`).
4. **Verificación exhaustiva:** Chequeo funcional de cada endpoint mediante **Postman** y registro de evidencias.

---

## 🏗️ 3. Arquitectura y Esquemas UML

### 3.1. Esquema de Integración Modular (Trabajo Individual ➡️ Repositorio Común)

Tal y como especifica la práctica, cada integrante del equipo desarrolló su funcionalidad antes de confluir en el controlador grupal integrado:

```mermaid
graph TD
    subgraph Desarrollos Individuales
        M1["👤 Member 1 (Gorka)<br><b>NurseController</b><br>+login(user, pw): boolean"]
        M2["👤 Member 2 (Roger)<br><b>NurseController</b><br>+getAll(): List&lt;Nurse&gt;"]
        M3["👤 Member 3 (Zehao)<br><b>NurseController</b><br>+findByName(name): Nurse"]
    end

    subgraph Repositorio Grupal Centralizado
        GC["👥 Grupo pr03 (Ignacio, Roger, Zehao, Gorka)<br><b>NurseController</b><br>─────────────────────────────<br>+getAll(): JsonResponse<br>+findByName(name: string): JsonResponse<br>+login(request: Request): JsonResponse"]
    end

    M1 -->|Integración Pull Request| GC
    M2 -->|Integración Pull Request| GC
    M3 -->|Integración Pull Request| GC
```

---

### 3.2. Diagrama de Clases UML

```mermaid
classDiagram
    direction TB
    class Kernel {
        <<MicroKernelTrait>>
        +boot()
        +registerBundles()
    }

    class AbstractController {
        <<Symfony Framework>>
        +json(data, status, headers, context) JsonResponse
    }

    class NurseController {
        -NurseRepository nurseRepository
        +getAll() JsonResponse
        +findByName(name: string) JsonResponse
        +login(request: Request) JsonResponse
        -toArray(nurse: Nurse) array
    }

    class JsonResponse {
        <<Symfony HttpFoundation>>
        +int statusCode
        +string content
    }

    class Request {
        <<Symfony HttpFoundation>>
        +getContent() string
    }

    class Nurse {
        <<Doctrine Entity>>
        -int id
        -string name
        -string email
        -string password
    }

    class NurseRepository {
        <<ServiceEntityRepository>>
        +findAll() Nurse[]
        +findOneBy(criteria) Nurse
        +findByEmailOrName(username: string) Nurse[]
    }

    class ImportNursesCommand {
        <<Console Command: app:import-nurses>>
        +execute(input, output) int
    }

    class Database {
        <<MariaDB/MySQL: tabla nurse>>
    }

    AbstractController <|-- NurseController
    NurseController ..> Request : recibe
    NurseController ..> JsonResponse : retorna
    NurseController --> NurseRepository : consulta
    NurseRepository ..> Nurse : devuelve
    NurseRepository ..> Database : Doctrine ORM
    ImportNursesCommand ..> Nurse : crea desde data/nurses.json
    ImportNursesCommand ..> Database : persiste
    Kernel ..> NurseController : enruta peticiones
```

---

### 3.3. Diagramas de Secuencia

#### A. Inicio de Sesión (`POST /nurse/login`)
```mermaid
sequenceDiagram
    autonumber
    actor Cliente as 📱 Cliente / Postman
    participant Router as 🧭 Symfony Router
    participant Ctrl as 🎮 NurseController
    participant Repo as 📚 NurseRepository
    participant BD as 🗄️ Base de datos

    Cliente->>Router: POST /nurse/login (body: username, password)
    Router->>Ctrl: login(Request $request)
    alt Falta username o password
        Ctrl-->>Cliente: 400 Bad Request {"success": false, "message": "..."}
    else Parámetros presentes
        Ctrl->>Repo: findByEmailOrName(username)
        Repo->>BD: SELECT ... WHERE email = :u OR name = :u
        BD-->>Repo: Enfermeros que coinciden (case-insensitive)
        Repo-->>Ctrl: Nurse[]
        loop Comprobación de contraseña
            Ctrl->>Ctrl: password_verify(password, hash)
        end
        alt Credenciales correctas
            Ctrl-->>Cliente: 200 OK {"success": true}
        else Credenciales incorrectas
            Ctrl-->>Cliente: 401 Unauthorized {"success": false}
        end
    end
```

#### B. Consulta General (`GET /nurse/index`)
```mermaid
sequenceDiagram
    autonumber
    actor Cliente as 📱 Cliente / Postman
    participant Router as 🧭 Symfony Router
    participant Ctrl as 🎮 NurseController
    participant Repo as 📚 NurseRepository
    participant BD as 🗄️ Base de datos

    Cliente->>Router: GET /nurse/index
    Router->>Ctrl: getAll()
    Ctrl->>Repo: findAll()
    Repo->>BD: SELECT ... FROM nurse
    BD-->>Repo: Filas
    Repo-->>Ctrl: Nurse[]
    Ctrl-->>Cliente: 200 OK [ Array con 1.000 enfermeros, sin password ]
```

#### C. Búsqueda por Nombre (`GET /nurse/name/{name}`)
```mermaid
sequenceDiagram
    autonumber
    actor Cliente as 📱 Cliente / Postman
    participant Router as 🧭 Symfony Router
    participant Ctrl as 🎮 NurseController
    participant Repo as 📚 NurseRepository
    participant BD as 🗄️ Base de datos

    Cliente->>Router: GET /nurse/name/{name}
    Router->>Ctrl: findByName(string $name)
    Ctrl->>Repo: findOneBy(name)
    Repo->>BD: SELECT ... WHERE name = :name (case-insensitive)
    BD-->>Repo: Fila o ninguna
    Repo-->>Ctrl: Nurse o null
    alt Enfermero localizado
        Ctrl-->>Cliente: 200 OK { "id": 1, "name": "Ana", "email": "..." }
    else No encontrado
        Ctrl-->>Cliente: 404 Not Found {"error": "Nurse not found"}
    end
```

---

### 3.4. Diagrama de Flujo Lógico de la Aplicación

```mermaid
flowchart TD
    Start([Petición HTTP entrante]) --> RouteCheck{¿Ruta registrada?}
    RouteCheck -- No --> R404[404 Route Not Found]
    RouteCheck -- Sí --> EndpointSwitch{Selección de Endpoint}

    %% Rama /nurse/index
    EndpointSwitch -- GET /nurse/index --> FindAll[NurseRepository::findAll]
    FindAll --> Resp200_All[200 OK: Lista total de enfermeros]

    %% Rama /nurse/name/{name}
    EndpointSwitch -- GET /nurse/name/{name} --> FindOne[NurseRepository::findOneBy name]
    FindOne --> Found{¿Existe?}
    Found -- Sí --> Resp200_Nurse[200 OK: Objeto del enfermero]
    Found -- No --> Err404_Nurse[404 Not Found: Nurse not found]

    %% Rama /nurse/login
    EndpointSwitch -- POST /nurse/login --> CheckBody{¿Vienen username y password?}
    CheckBody -- No --> Err400[400 Bad Request: Username and password required]
    CheckBody -- Sí --> FindUser[NurseRepository::findByEmailOrName]
    FindUser --> MatchCreds{¿password_verify correcto?}
    MatchCreds -- Sí --> Resp200_Log[200 OK: success true]
    MatchCreds -- No --> Err401[401 Unauthorized: success false]
```

---

## ⚡ 4. Especificación de la API REST

### Endpoints Disponibles

| Método | Ruta | Parámetros | Códigos HTTP | Descripción |
|---|---|---|---|---|
| `GET` | `/nurse/index` | *Ninguno* | `200` | Devuelve el catálogo completo de 1.000 enfermeros (`id`, `name`, `email`). |
| `GET` | `/nurse/name/{name}` | Path: `{name}` | `200`, `404` | Localiza un enfermero por su nombre (*case-insensitive*). No devuelve `password`. |
| `POST` | `/nurse/login` | Body JSON: `username`, `password` | `200`, `400`, `401` | Inicia sesión verificando credenciales contra email o nombre. |

### Colección de Pruebas Postman

El repositorio incluye la suite de pruebas lista para importar:
* 📄 [`docs/postman/nurse-api.postman_collection.json`](docs/postman/nurse-api.postman_collection.json)
* 📸 Evidencias de ejecución: [`docs/postman/login`](docs/postman/login), [`docs/postman/readAll`](docs/postman/readAll), [`docs/postman/readByName`](docs/postman/readByName).

---

## 💾 5. Modelo de Datos (tabla `nurse`)

Los datos se guardan en una base de datos **MariaDB/MySQL** gestionada con **Doctrine ORM**. La tabla se genera desde la entidad [`src/Entity/Nurse.php`](src/Entity/Nurse.php) mediante una migración:

```sql
CREATE TABLE nurse (
  id INT AUTO_INCREMENT NOT NULL,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(180) NOT NULL,
  password VARCHAR(255) NOT NULL,
  UNIQUE INDEX uniq_nurse_email (email),
  PRIMARY KEY (id)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB
```

Los 1.000 enfermeros de prueba de [`data/nurses.json`](data/nurses.json) se cargan con `php bin/console app:import-nurses`.

> [!NOTE]
> Las contraseñas se guardan **cifradas con hash** (`password_hash`) y se comprueban con `password_verify`. La API nunca devuelve el campo `password`.

---

## 🔄 6. Flujo de Trabajo en GitHub

El equipo siguió una metodología estricta basada en ramas, issues y pull requests:

```mermaid
gitGraph
    commit id: "Init Symfony 7.4"
    branch staging
    checkout staging
    commit id: "Config .env.example"
    branch 2-implement-read-all-nurses-from-file
    checkout 2-implement-read-all-nurses-from-file
    commit id: "roger/Feat: read all #2"
    checkout staging
    merge 2-implement-read-all-nurses-from-file id: "PR #16"
    branch 3-implement-nurse-login-from-file
    checkout 3-implement-nurse-login-from-file
    commit id: "gorka/Feat: login #3"
    checkout staging
    merge 3-implement-nurse-login-from-file id: "PR #17"
    branch 4-implement-find-nurse-by-name-from-file
    checkout 4-implement-find-nurse-by-name-from-file
    commit id: "zyin-08/Feat: find by name #4"
    checkout staging
    merge 4-implement-find-nurse-by-name-from-file id: "PR #15"
    branch 5-verify-endpoints-with-postman
    checkout 5-verify-endpoints-with-postman
    commit id: "ibrenas/Add postman collection #5"
    checkout staging
    merge 5-verify-endpoints-with-postman id: "PR #33"
    checkout main
    merge staging id: "Release 1.0"
```

* **Gestión y Liderazgo:** Ignacio Breñas ejerció el rol de **Project Manager**, encargándose de planificar las entregas, estructurar y crear las ramas de trabajo de cada desarrollador, repartir los issues en GitHub Projects y supervisar la integración continua asegurando la estabilidad y el correcto funcionamiento de todo el proyecto.
* **Convención de commits:** `usuario/Tipo: descripción #issue` (ej. `roger/Feat: implement read all nurses from file #2`).
* **Tablero Kanban:** Gestión mediante GitHub Projects (`Todo`, `In progress`, `Done`).
* **Integración controlada:** Ramas individuales de feature ➡️ `staging` mediante Pull Request con revisión de código ➡️ `main` para versiones estables.

---

## 🚀 7. Instalación y Ejecución Rápida

### Requisitos Previos
* **PHP >= 8.2** con extensiones comunes (`json`, `mbstring`, `ctype`).
* **Composer** (v2 o superior).
* **MariaDB** o **MySQL** (por ejemplo, el de XAMPP).
* *(Opcional)* **Symfony CLI**.

### Puesta en Marcha

```bash
# 1. Clonar el repositorio
git clone https://github.com/Ignaciobrenas/Nurse-Simulator.git
cd Nurse-Simulator

# 2. Instalar dependencias de Symfony
composer install

# 3. Configurar entorno local
cp .env.example .env
# Añade DATABASE_URL en .env.local (no se sube a Git), por ejemplo para XAMPP:
# DATABASE_URL="mysql://root:@127.0.0.1:3306/nurse_simulator?serverVersion=10.4.32-MariaDB&charset=utf8mb4"

# 4. Crear la base de datos, la tabla y cargar los enfermeros
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
php bin/console app:import-nurses

# 5. Iniciar el servidor de desarrollo
symfony server:start
# O alternativamente con PHP CLI:
php -S localhost:8000 -t public
```

### Probar Endpoints con cURL

```bash
# Listado completo de enfermeros
curl -i http://localhost:8000/nurse/index

# Buscar enfermero por nombre
curl -i http://localhost:8000/nurse/name/Ana

# Iniciar sesión
curl -i -X POST http://localhost:8000/nurse/login \
  -H "Content-Type: application/json" \
  -d '{"username": "ana@hospital.com", "password": "ana123"}'
```

---

## 📂 8. Estructura del Proyecto

```
Nurse-Simulator/
├── bin/
│   └── console                    # CLI de Symfony
├── config/                        # Configuración de rutas y servicios
│   ├── packages/
│   ├── routes.yaml
│   └── services.yaml
├── data/
│   └── nurses.json                # Dataset inicial (1.000 enfermeros) para app:import-nurses
├── docs/
│   ├── general/
│   │   ├── DOCUMENTACION_ES.md    # Memoria técnica completa en español
│   │   ├── DOCUMENTATION_EN.md    # Complete technical documentation in English
│   │   ├── Nurse-Simulator.docx   # Documento Word editable
│   │   └── Nurse-Simulator_...pdf # Documento PDF oficial de entrega
│   └── postman/                   # Colección y capturas de prueba
│       ├── login/
│       ├── readAll/
│       ├── readByName/
│       └── nurse-api.postman_collection.json
├── migrations/                    # Migraciones de Doctrine (tabla nurse)
├── public/
│   └── index.php                  # Front Controller
├── src/
│   ├── Command/
│   │   └── ImportNursesCommand.php # Comando app:import-nurses
│   ├── Controller/
│   │   └── NurseController.php    # Controlador con todos los endpoints
│   ├── Entity/
│   │   └── Nurse.php              # Entidad Doctrine
│   ├── Repository/
│   │   └── NurseRepository.php    # Consultas de enfermeros
│   └── Kernel.php                 # Kernel de la aplicación
├── .env.example                   # Plantilla de variables de entorno
├── composer.json                  # Dependencias y autoloading
├── LICENSE                        # Licencia MIT
└── README.md                      # Presentación visual del proyecto
```

---

<div align="center">
  <sub>Desarrollado con dedicación por el Grupo pr03 • STUCOM Pelai • DAW 2 (2026/27)</sub>
</div>
