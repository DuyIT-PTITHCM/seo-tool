# SEO Ranking Measurement System

This SEO Ranking Measurement System is a tool developed by ABC Company for measuring search rankings. It allows users to enter a URL and keywords, and displays the rankings of the specified URL in Google and Yahoo search results for the target keywords. The measured information is stored in a MySQL database.

## Database Structure

The MySQL database for the SEO Ranking Measurement System consists of two main tables:

1. Measurements:
   - Id (Primary Key): Unique identifier for each measurement.
   - URL: The entered URL for measurement.

2. Rankings:
   - Id (Primary Key): Unique identifier for each ranking result.
   - MeasurementID (Foreign Key): References the MeasurementID in the Measurements table.
   - Keyword: The keyword used for the ranking measurement.
   - SearchEngine: The search engine where the ranking was measured (e.g., Google, Yahoo).
   - Rank: The ranking position of the URL in the search results.
   - SearchResultsLink: The link to the search results if the ranking is available.

The Measurements table stores information about the URL entered for measurement, while the Rankings table stores the ranking results associated with each measurement.
![image](https://github.com/DuyIT-PTITHCM/seo-tool/assets/54759692/84c8b98b-42e5-4610-b1da-f5483fb3f97f)

## RUNNING LOCAL
- install docker desktop : https://docs.docker.com/desktop/install/mac-install/
- install node v18
- cd frontend/
- npm install
- cd ..
- Run command line:
  - sudo chmod 777 ./start.sh
  - sudo chmod 777 ./stop.sh
  - sudo ./start.sh --build
- Stop Services :
  - ./stop.sh
## RUNNING COMMAND LINE
- ./composer xxx
  - ./composer install
- ./npm xxx
  - ./npm install
- ./yarn xxx
  - ./yarn install
- ./artisan xxx
  - ./artisan cache:clear
  - ./artisan make:migration
  ## improve system
  - Xử lý lỗi và báo cáo
  - Tích hợp nhiều công cụ tìm kiếm: Hệ thống ranking hiện tại của bạn có thể chỉ hỗ trợ một công cụ tìm kiếm như Google hoặc Yahoo.
  - Giao diện người dùng cải tiến:
  - frontend từ reactjs chuyển sang nextjs để google có thể tìm thấy trang của mình
  - xử dụng cron để từ các url và keyword đã được lưu trong table chạy command mỗi đêm 23h để qúet lại ranking, nếu người dùng nhập keyword và url đã có trong db thì không cần phải cào dữ liệu lại 
