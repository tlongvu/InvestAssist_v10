from selenium import webdriver
from selenium.webdriver.chrome.service import Service
from selenium.webdriver.chrome.options import Options
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
from webdriver_manager.chrome import ChromeDriverManager
import time
import sys

def get_stock_price(symbol):
    # Setup Chrome options
    chrome_options = Options()
    chrome_options.add_argument("--headless")  # Run in headless mode
    chrome_options.add_argument("--disable-gpu")
    chrome_options.add_argument("--no-sandbox")
    chrome_options.add_argument("--window-size=1920,1080")
    
    # Initialize driver
    service = Service(ChromeDriverManager().install())
    driver = webdriver.Chrome(service=service, options=chrome_options)
    
    # try:
    #     url = f"https://fireant.vn/ma-chung-khoan/{symbol}"
    #     print(f"Loading {url}...")
    #     driver.get(url)
        
    #     # Wait for price element to load
    #     # Using a fairly generic 'span' in the header area might be brittle, so we'll try the specific path found
    #     # Selector found: div#__next main div div div div:nth-child(2) div span
    #     # It's safer to look for the large price number specifically. 
    #     # Often these are in a container with specific classes, but class names can be dynamic (e.g. styled-components).
    #     # We will try to wait for any text that looks like a price in the top section.
        
    #     wait = WebDriverWait(driver, 20)
        
    #     # This xpath looks for a span that contains a decimal number in the main container
    #     # Adapting the selector found: div#__next main div div div div:nth-child(2) div span
    #     selector = "div#__next main div div div div:nth-child(2) div span"
        
    #     print("Waiting for price element...")
    #     price_element = wait.until(EC.presence_of_element_located((By.CSS_SELECTOR, selector)))
        
    #     # Get text
    #     price_text = price_element.text
    #     return price_text
        
    # except Exception as e:
    #     print(f"Error: {e}")
    #     return None
    # finally:
    #     driver.quit()



def get_stock_price(symbol, max_retry=2):
    url = f"https://fireant.vn/ma-chung-khoan/{symbol}"

    for attempt in range(1, max_retry + 1):
        driver = None
        try:
            print(f"[Attempt {attempt}] Loading {url}")

            chrome_options = Options()
            chrome_options.add_argument("--headless")
            chrome_options.add_argument("--disable-gpu")
            chrome_options.add_argument("--no-sandbox")
            chrome_options.add_argument("--window-size=1920,1080")

            service = Service(ChromeDriverManager().install())
            driver = webdriver.Chrome(service=service, options=chrome_options)

            driver.get(url)

            wait = WebDriverWait(driver, 20)

            selector = "div#__next main div div div div:nth-child(2) div span"

            print("Waiting for price element...")
            price_element = wait.until(
                EC.presence_of_element_located((By.CSS_SELECTOR, selector))
            )

            price_text = price_element.text.strip()

            if price_text:
                print(f"Success: {symbol} = {price_text}")
                return price_text
            else:
                raise Exception("Price text is empty")

        except Exception as e:
            print(f"[Attempt {attempt}] Error: {e}")

            if attempt < max_retry:
                print("Retrying...")
                time.sleep(2)  # nghỉ 2 giây rồi thử lại
            else:
                print("Failed after retries")
                return None

        finally:
            if driver:
                driver.quit()
                
                
# if __name__ == "__main__":
#     symbol = "BID"
#     if len(sys.argv) > 1:
#         symbol = sys.argv[1]
        
#     print(f"Fetching price for {symbol}...")
#     price = get_stock_price(symbol)
    
#     if price:
#         print(f"Current price of {symbol}: {price}")
#     else:
#         print("Failed to retrieve price.")
