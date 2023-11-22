from linkedin_scraper import Person, actions
from selenium import webdriver
from webdriver_manager.chrome import ChromeDriverManager
import sys

username=sys.argv[1]

driver = webdriver.Chrome(ChromeDriverManager().install())


email = "sonik.sarungale@gmail.com"
password = "$onik@2k16"
actions.login(driver, email, password)
person = Person(linkedin_url="https://www.linkedin.com/in/"+username, name=None, about=[], experiences=[], educations=[], driver=driver, scrape=True)
print(person)
print(person.scrape())
