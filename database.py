import os
from sqlalchemy import create_engine
from sqlalchemy.ext.declarative import declarative_base
from sqlalchemy.orm import sessionmaker

# Ambil konfigurasi database dari .env (default: programkode3)
db_user = "root"
db_password = ""
db_host = "127.0.0.1"
db_port = "3306"
db_name = "programkode3"

env_path = os.path.join(os.path.dirname(__file__), ".env")
if os.path.exists(env_path):
    with open(env_path, "r", encoding="utf-8") as f:
        for raw_line in f:
            line = raw_line.strip()
            if line.startswith("DB_HOST="):
                val = line.split("=", 1)[1].strip().strip('"').strip("'")
                if val:
                    db_host = val
            elif line.startswith("DB_PORT="):
                val = line.split("=", 1)[1].strip().strip('"').strip("'")
                if val:
                    db_port = val
            elif line.startswith("DB_DATABASE="):
                val = line.split("=", 1)[1].strip().strip('"').strip("'")
                if val:
                    db_name = val
            elif line.startswith("DB_USERNAME="):
                val = line.split("=", 1)[1].strip().strip('"').strip("'")
                if val:
                    db_user = val
            elif line.startswith("DB_PASSWORD="):
                val = line.split("=", 1)[1].strip().strip('"').strip("'")
                db_password = val

SQLALCHEMY_DATABASE_URL = f"mysql+pymysql://{db_user}:{db_password}@{db_host}:{db_port}/{db_name}"

# 1. Buat engine SQLAlchemy
engine = create_engine(SQLALCHEMY_DATABASE_URL, pool_pre_ping=True, pool_recycle=3600)

# 2. Local Session
SessionLocal = sessionmaker(autocommit=False, autoflush=False, bind=engine)

# 3. Base Class untuk model.
Base = declarative_base()

# 4. Dependency untuk endpoint FastAPI
def get_db():
    db = SessionLocal()
    try:
        yield db
    except Exception:
        db.rollback()
        raise
    finally:
        db.close()
