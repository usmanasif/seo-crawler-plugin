
# Explanation of SEO Crawler Plugin


## Problem to Be Solved
- The problem to be solved is to build a WordPress plugin or app that allows website administrators to manually crawl their homepage, extract internal hyperlinks, store the results in a database, and display them. 
- This is crucial for improving SEO rankings as it helps administrators identify and optimize internal linking structures.

## Technical Specification
### Admin Page
- I have added a back-end admin page accessible to administrators.
- The admin page provides the ability to manually trigger a crawl and view the results.
When an admin initiates a crawl, the plugin:
- Deletes the results from the last crawl.
- Deletes the sitemap.html file if it exists.
- Extracts all internal hyperlinks present in the homepage and stores the results in the database.
- Displays the results on the admin page.
- Saves the homepage as an .html file on the server.
- The plugin sets up an automatic crawl task to run every hour.
- In case of errors during crawling or displaying results, the plugin handles proper exceptions.
- Used modern object-oriented programming (OOP) principles and followed PSR standards for clean, organized code.
- Prioritized error handling to ensure that the administrator is aware of any issues and can take appropriate actions.

## Technical Decisions and their Reasoning
- I opted to use a WordPress plugin as it integrates seamlessly with the WordPress environment, making it easy for administrators to use.
- WordPress plugin solution seamlessly integrates with existing WordPress websites. This integration ensures that users do not need to migrate to a different platform or re-implement their websites, which can be a costly and time-consuming process.
- WordPress has a vast ecosystem of themes, plugins, and extensions. By being part of this ecosystem, our plugin can interact with other WordPress features, creating new synergies and opportunities for website owners.
- Most of the Administrators are already familiar with the WordPress dashboard. By developing a plugin for WordPress, I leverage this familiarity, making it easier for users to adopt and utilize our tool.
- WordPress plugins benefit from the built-in plugin management system. Administrators can easily update our plugin through the WordPress dashboard, ensuring that they always have access to the latest features and security enhancements. With a standalone PHP app, updating and maintaining the application would require more manual effort.

## Achieving the Desired Outcome
- My solution addresses the administrator's needs by offering:
- A user-friendly back-end admin page for manual crawling.
- Automated crawls every hour to keep data up-to-date.
- Database storage of crawled data for future analysis.
- An error-handling mechanism to guide administrators in case of issues.
- Saving the homepage for further reference.
- The plugin offers a comprehensive solution for SEO improvement by allowing administrators to monitor internal hyperlinks and take actions to enhance their website's SEO rankings.