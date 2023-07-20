function print_page_view(url) {
const d = new window.printd.Printd();
d.printURL(url, ({ launchPrint }) => {
launchPrint();
});
}
