$$("input[type=radio]").each(function(el){
    if (el.up("label")) {
        el.up("label").addClassName("custom-radio-label").down("input[type=radio]").insert({
            after: "<span class='custom-tick'></span>"
        });
    }
});

$$("input[type=checkbox]").each(function(el){
    if (el.up("label")) {
        el.up("label").addClassName("custom-checkbox-label").down("input[type=checkbox]").insert({
            after: "<span class='custom-tick'></span>"
        });
    }
});