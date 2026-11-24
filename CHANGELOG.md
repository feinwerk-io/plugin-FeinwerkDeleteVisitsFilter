# Changelog

## 5.1.0

- Preview: the sample shows browser and operating system by name (the code on hover) and covers every website with matches instead of only the first one.
- Admin page: once the matches are shown, the delete button is the only filled button; the button that deletes in the confirmation dialog is red like the delete button of the page; the dialogs before deleting, stopping and continuing a deletion show the action first and Cancel last, as Matomo does, and put the focus on the safe choice in every supported Matomo version; the date and saved segment selects line up with the other fields; an untouched segment condition no longer shows a half finished definition.
- History: when no deletion matches the filters, the page says so and offers to show all; numbers in the details are formatted.
- Wording: the English page says "Stop" for stopping a deletion and "Started via" for the filter; date ranges read "du ... au ..." in French, "del ... al ..." in Spanish and "dal ... al ..." in Italian.
- Keyboard and screen readers: the tabs work with the arrow keys and show the focus, the details link says whether it is open, and each remove button names its website.
- A deletion survives a short loss of the database connection ("MySQL server has gone away"): it connects again and continues. If the database stays away, the deletion is marked as failed and can be continued from the history, and the next deletion starts at once instead of after up to 10 minutes.
- API for scripts and AI assistants: createJob takes expectedVisits (nothing is queued when the number of matching visits differs) and dryRun=1 (only counts) and returns the created job; getJobs and createJob refuse unknown filter and source values with the allowed ones; every method describes its parameters and result, and the README shows example calls; an internal helper no longer appears in Matomo's API list.
- The Feinwerk Connector tip leads super users to the one-click installer under Administration, Feinwerk Plugins, where it is offered.
- The connector installer shows its progress after the click and takes no second click; it also updates a connector that no longer fits this Matomo instead of activating it.
- The tip below the admin page drops its sentence on very narrow screens instead of cutting it; the Feinwerk tip on the start page of the administration sits below Matomo's own notices.
- A deletion that makes no progress for two minutes now says since when and that it continues by itself; the admin page no longer says that another process is working then. A deletion that searches a long period for rare matches counts as working, not as stalled.
- History: the outcome and the actions stay in view (who started a deletion and how long it took are shown under the time and the count), dates no longer break in the middle, and on narrow screens each deletion is shown as one block with its fields aligned.
- Better contrast for the instruction in the confirmation dialog and for the website choice; the website name filter wraps on narrow screens; French texts put a space before colons.

## 5.0.5

- Super users no longer see the Feinwerk Connector tip where Matomo's Marketplace offers only a download instead of its install button (automatic updates switched off or a multi-server setup).
- The plugin is now also available in French, Spanish and Italian.
- Super users can install and activate the free Feinwerk Connector with one click under Administration, Feinwerk Plugins, while it is not active; `connector_installer = 0` hides the entry.
- The Feinwerk Connector link in the tip below the admin page opens in the same tab when it leads a super user into their own Matomo Marketplace.

## 5.0.4

- The tip below the admin page also points to the Feinwerk Connector on the Matomo Marketplace while it is not installed and this Matomo can install plugins from its Marketplace (not in Matomo for WordPress, where it cannot install plugins yet).
- The tip below the admin page starts with the Feinwerk Plugins logo; on narrow screens only its round mark.
- The hide control of the tip below the admin page stays right after its link, also next to other Feinwerk plugins.

## 5.0.3

- "1 website added." instead of "1 websites added." when a name filter adds a single website.
- API errors for scripts and integrations come with a fitting HTTP status: 400 for invalid input, 404 for an unknown deletion.
- The Feinwerk tip on the start page of Matomo's administration now appears at the top of the page.
- The Feinwerk tips on the start page of the administration and on matching reports show the Feinwerk Plugins logo.
- The feedback question and the tip below the admin page are quieter and always take a single line; the support link has a help icon.

## 5.0.2

- History: the start of a deletion is shown like the date ranges (for example 2026-11-11 22:57), and website names no longer wrap after every word.
- The total of the matching visits lines up with the counts above it.
- The confirmation field has some room next to its label, and long names of saved segments no longer run into the arrow of the list.

## 5.0.1

- The feedback question and the tip under the admin page now take a single compact line each.

## 5.0.0

- New version numbers that follow Matomo: version 5.x is for Matomo 5, version 6.x for Matomo 6.
- Below the admin page you can tell us whether deleting visits works as expected (thumbs up or down), and "Support" opens the Feinwerk contact form. The question can be switched off with `feedback = 0` (README, "Feedback and support").
- Short tips on other Feinwerk offers for administrators: one line under the admin page, one banner on the start page of Matomo's administration and one box at the end of the Campaigns, Ecommerce overview and Goals overview reports. A tip can be hidden for 30 days, and `promotions = 0` switches them off (README, "Tips from Feinwerk").
- The plugin's settings in General settings are headed "Feinwerk Delete Visits Filter".

## 2.2.3

- The history fits the page: its filters line up, and wide tables scroll inside their box instead of widening the page.
- The custom date range shows both calendars side by side.
- The field for website names has a label for screen readers.
- The labels of the filters in the history start at the left edge of their field.
- The list of saved segments offers "Please choose" as its empty entry, and empty values show a plain hyphen.
- The console job list and the Activity Log show date ranges as "2026-01-01 to 2026-01-31".
- Small wording improvements in the plugin description, on the admin page and in the settings help.

## 2.2.2

- This version is for Matomo 5. Matomo 6 support comes with the next major version of the plugin.

## 2.2.1

- Clearer segment input: a switch between the segment editor and text mode; in text mode a full-width field that grows with the segment, with a short syntax hint and a character counter (segments longer than 8,192 characters are refused right away).
- The admin page is now compatible with Matomo's dark mode and follows the theme chosen in Matomo (light, dark or automatic).
- Segments are shown in one consistent, quieter style in the progress area, the history and the confirmation dialog.
- Number column headers are right-aligned like their values.

## 2.2.0

- New admin page:
  - choose one website, several websites (also all whose name contains a text) or all websites;
  - pick the date range from presets such as "last 30 days" or "last year", or from a calendar;
  - use a saved segment, the segment editor or plain text.
- "Check matches" shows the matching visits per website, a sample of visits (with the visitor profile) and a link to the visitor log before anything is deleted.
- Deleting 10,000 or more visits must be confirmed by typing the number of visits.
- Running deletions show their progress, speed and estimated remaining time, can be cancelled and continue after the page is reloaded. If the console or the background task is already working, the page shows that instead of competing with it.
- New history of all deletions with filters, details, "use as template" and "try again" for failed deletions.
- The admin page refuses segment conditions without a value (for example from an untouched row in the segment editor); deliberate "is empty" conditions still work in the console and the API.
- Website names with special characters are shown correctly in previews and console output.
- Faster deletions with negated page conditions (e.g. `pageUrl!@/spam`) over long date ranges.
- The admin page responds faster when several deletions are queued: it no longer starts another queued deletion late in a request.

## 2.1.0

- Deletions now run as jobs that keep going in the background: if the page is closed or a request times out, the
  hourly background task continues where the deletion stopped. Very large deletions can run without a time limit via
  `./console feinwerkdeletevisitsfilter:work`.
- Date ranges are interpreted in each website's timezone (previously UTC). The affected days are marked for
  re-processing in the website's timezone, including roll-up websites.
- Data of other plugins that store visit data in their own log tables (e.g. forms, heatmaps, media, A/B tests) is
  deleted together with the visits.
- Console: `feinwerkdeletevisitsfilter:delete` accepts several website IDs or `all`, shows the matches per website,
  asks for confirmation (`--yes` skips it) and offers `--dry-run`. New commands `feinwerkdeletevisitsfilter:work`
  and `feinwerkdeletevisitsfilter:jobs`. Scripts that run `feinwerkdeletevisitsfilter:delete` without a terminal must
  now pass `--yes`; without it the command stops with an error instead of deleting.
- New API methods `previewDeletion`, `createJob`, `processJobs`, `getJob`, `getJobs`, `cancelJob`, `retryJob`.
  `findVisits` and `deleteVisits` are deprecated but keep working.
- New settings: background processing time per hour and default batch size.
- Finished, failed and cancelled deletions appear in the Activity Log if that plugin is installed.
- Requires Matomo 5.1 or newer.
- Segments that Matomo would silently change are refused: a segment without a valid condition (it would match every visit) and segments longer than 8192 characters.
- Segments with a negated page condition (e.g. `pageUrl!@/spam`) follow Matomo's report semantics: a visit matches if none of its actions matches (this also includes visits without any page view).
- The admin page now shows errors returned by the API (previously some errors were shown as a completed deletion with 0 visits), and the deprecated `deleteVisits` API method reports an error unless the deletion finished within the request.

## 2.0.0

- The plugin is now published by Feinwerk.io and has been renamed from `DeleteVisitsFilter` to
  `FeinwerkDeleteVisitsFilter`.
- API methods are now `FeinwerkDeleteVisitsFilter.countVisits` and `FeinwerkDeleteVisitsFilter.deleteVisits`.
- The console command is now `feinwerkdeletevisitsfilter:delete`; the previous name
  `deletevisitsfilter:delete` keeps working as an alias.
- Upgrading from `DeleteVisitsFilter`: activate `FeinwerkDeleteVisitsFilter`, then uninstall
  `DeleteVisitsFilter`. No settings or data need to be migrated.

## 1.0.0

- Initial release.
