## FAQ

__Can deleted visits be restored?__

No. Visits are removed from Matomo's raw data tables for good. Make a database backup before large deletions.

__How is this different from Matomo's GDPR tool?__

It uses the same deletion routine, but selects visits by segment, date range and website instead of one person at a time.

__Are my reports corrected?__

Yes. The affected days are marked for processing again, including roll-up websites; the next archiving run rebuilds the reports.

__What happens if I close the page?__

The deletion continues: an hourly background task goes on where it stopped (unless a super user switched background processing off in the settings), and very large deletions can run on the console without a time limit.

__Who can delete visits?__

Super users only.

__Does a segment on pages delete only those pages?__

No. A segment on page or conversion data deletes the whole matching visit.

__Where do I find more Feinwerk plugins?__

Install Feinwerk Connector from the Matomo Marketplace. It lists all Plugins for Matomo by Feinwerk, free and paid, and installs and updates them with one click.
