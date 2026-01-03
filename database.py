from sqlalchemy import create_engine
from sqlalchemy.orm import scoped_session, sessionmaker

engine = create_engine("mysql+pymysql://user:password@localhost/access_control")
db_session = scoped_session(sessionmaker(bind=engine))