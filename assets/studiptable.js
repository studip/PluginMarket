jQuery.fn.extend({
    studipTable: function (opt) {
        return new STUDIP.table(this, opt);
    }
});

STUDIP.table = function (element, opt) {

    this.maxSize = 25;
    this.sortable = true;

    $.extend(this, opt);
    this.element = element;
    var self = this;

    // Find rows and cols
    this.rows = element.find('tbody tr:not(:has(th))');
    this.cols = element.find('thead tr td');
    this.headers = element.find('th');

    // Init pagination
    if (false && this.rows.length > this.maxSize) {
        this.pages = Math.ceil(this.rows.length / this.maxSize);

        this.pageination = $('<div>').addClass('tablepagination');
        for (var i = 0; i < this.pages; i++) {
            this.pageination.append($('<a>').html(i + 1).prop('data-page', i + 1).click(function (e) {
                e.preventDefault();
                self.page($(this).prop('data-page'));
            }));
        }

        element.append($('<tfoot>').append($('<tr>').append($('<td>').prop('colspan', this.cols.length).html(this.pageination))));
        this.page(1);
    }

    // Init search fields
    if (this.searchInput) {
        $(this.searchInput).keyup(function (e) {
            var keyword = $(self.searchInput).val();
            if (keyword.length > 1) {
                self.rows.hide();
                self.rows.filter(':contains(' + keyword + ')').show();
            } else {
                self.rows.show();
            }
        });
    }

    // Apply sortable
    if (this.sortable) {
        this.headers.click(function (e) {

            var asc = true;
            // Check if this is already sorted
            if ($(this).attr('data-sort') === 'asc') {
                asc = false;
                self.headers.removeAttr('data-sort');
                $(this).attr('data-sort', 'desc');
                $(this).parent().children('th').removeClass('sortasc sortdesc');

                $(this).addClass('sortdesc');

            } else {
                self.headers.removeAttr('data-sort');
                $(this).attr('data-sort', 'asc');
                $(this).parent().children('th').removeClass('sortasc sortdesc');

                $(this).addClass('sortasc');
            }

            var sortType = $(this).attr('data-sort-type');

            var headerIndex = $(this).index() + 1;
            var body = self.rows.parent();
            self.rows.sort(function (a, b) {
                var sort1,sort2;
                var attr = $(a).find('td:nth-child(' + headerIndex + ')').attr('data-sort');
                if (typeof attr !== typeof undefined && attr !== false) {
                    sort1 = $(a).find('td:nth-child(' + headerIndex + ')').attr('data-sort');
                    sort2 = $(b).find('td:nth-child(' + headerIndex + ')').attr('data-sort');
                } else if (sortType === 'int') {
                    sort1 = parseFloat('0' + $(a).find('td:nth-child(' + headerIndex + ')').text().trim());
                    sort2 = parseFloat('0' + $(b).find('td:nth-child(' + headerIndex + ')').text().trim());
                } else if (sortType === 'sorter') {
                    sort1 = parseFloat('0' + $(a).find('td:nth-child(' + headerIndex + ')').data('sorter'));
                    sort2 = parseFloat('0' + $(b).find('td:nth-child(' + headerIndex + ')').data('sorter'));
                } else {
                    sort1 = $(a).find('td:nth-child(' + headerIndex + ')').text().trim().toLowerCase();
                    sort2 = $(b).find('td:nth-child(' + headerIndex + ')').text().trim().toLowerCase();

                }
                if(sort1 == sort2) return 0;
                if (asc) return sort1 > sort2 ? 1 : -1;
                else return sort1 < sort2 ? 1 : -1;

            });
            self.rows.detach().appendTo(body);
        });
    }

};

STUDIP.table.prototype.page = function (number) {
    this.rows.show();
    var maxSize = this.maxSize;
    this.rows.filter(function (index) {
        return index < (number - 1) * maxSize || index >= number * maxSize;
    }).hide();
};