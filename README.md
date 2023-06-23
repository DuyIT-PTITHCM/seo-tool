# seo-tool
Entities:
- Measurement: 
  - Id (Primary Key)
  - URL

- Ranking:
  - Id (Primary Key)
  - MeasurementID (Foreign Key referencing Measurement.MeasurementID)
  - Keyword
  - SearchEngine
  - Rank
  - SearchResultsLink

Relationships:
- One Measurement can have multiple Rankings.
- Each Ranking is associated with one Measurement.
