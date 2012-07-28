Jacli = new Class({
    changeApp:function (e, cName) {
        var sel = e.options[e.selectedIndex].value;
        var container = document.id(cName);

        if (0 == sel)
            return false;

        parts = sel.split('|');

        var f = parts[0].charAt(0).toUpperCase();

        document.id('appLabel').set('text', f + parts[0].substr(1));

        new Request.JSON({
            url:'index.php?do=get&item=appconfig&app=' + parts[0],

            onRequest:function () {
                // @todo spinner
            },

            onComplete:function (response) {
                if (response.status) {
                    container.innerHTML = '<p style="color: red;">' + response.debug + '</p>' + response.text;
                }
                else {
                    container.set('html', response.text);
                }
            },

            onFailure:function () {
                // @todo error handling
                container.set('html', 'The request failed');
            }

        }).send();
    }
});

Jacli = new Jacli;
