//https://stackoverflow.com/questions/1130921/is-the-callback-on-jquerys-getscript-unreliable-or-am-i-doing-something-wrong
//...didn't quite work for me. I find this recursive approach ensures that I download scripts in the correct
//sequence
ko.dt.getScripts = function (progress, scripts, callback) {
    //Don't run the getScript routine at all if there are no scripts.
    if (scripts.length === 0) {
        callback(0);
    } else {
        var script = scripts[progress];
        $.getScript(script)
                .done(function (fileContents, textStatus) {
                    progress += 1;
                    if (progress < scripts.length) {
                        ko.dt.getScripts(progress, scripts, callback);
                    } else {
                        callback(progress);
                    }
                });
    }
};