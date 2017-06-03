(function () {

    // Grab the data
    var labels = [],
        data = [];
    jQuery("#ab_chart_data tfoot th").each(function () {
        labels.push(jQuery(this).text());
    });
    jQuery("#ab_chart_data tbody td").each(function () {
        data.push(jQuery(this).text());
    });

    // Draw
    var width = jQuery('#ab_chart').parent().width() + 8,
        height = 140,
        leftgutter = 0,
        bottomgutter = 22,
        topgutter = 22,
        color = '#0073aa',
        r = Raphael("ab_chart", width, height),
        txt = {font: 'bold 12px "Open Sans", sans-serif', fill: "#32373c"},
        X = (width - leftgutter * 2) / labels.length,
        max = Math.max.apply(Math, data),
        Y = (height - bottomgutter - topgutter) / max;

    /* Max Wert */
    r
    .text(16, 16, max)
    .attr(
        {
            'font': 'normal 10px "Open Sans", sans-serif',
            fill: "#b4b9be"
        }
    );

    var path = r.path().attr({stroke: color, "stroke-width": 2, "stroke-linejoin": "round"}),
        bgp = r.path().attr({stroke: "none", opacity: .3, fill: color}),
        label = r.set(),
        lx = 0, ly = 0,
        is_label_visible = false,
        leave_timer,
        blanket = r.set();
    label.push(r.text(60, 12, "24× Spam").attr(txt));
    label.push(r.text(60, 27, "23.12.2013").attr(txt).attr({fill: color}));
    label.hide();
    var frame = r.popup(100, 100, label, "right").attr({fill: "#fff", stroke: "#444", "stroke-width": 1}).hide();

    var p, bgpp;
    for (var i = 0, ii = labels.length; i < ii; i++) {
        var y = Math.round(height - bottomgutter - Y * data[i]),
            x = Math.round(leftgutter + X * (i + .5));
        if (!i) {
            p = ["M", x, y, "C", x, y];
            bgpp = ["M", leftgutter + X * .5, height - bottomgutter, "L", x, y, "C", x, y];
        }
        if (i && i < ii - 1) {
            var Y0 = Math.round(height - bottomgutter - Y * data[i - 1]),
                X0 = Math.round(leftgutter + X * (i - .5)),
                Y2 = Math.round(height - bottomgutter - Y * data[i + 1]),
                X2 = Math.round(leftgutter + X * (i + 1.5));
            var a = getAnchors(X0, Y0, x, y, X2, Y2);
            p = p.concat([a.x1, a.y1, x, y, a.x2, a.y2]);
            bgpp = bgpp.concat([a.x1, a.y1, x, y, a.x2, a.y2]);
        }
        var dot = r.circle(x, y, 4).attr({fill: "#fff", stroke: color, "stroke-width": 1});
        blanket.push(r.rect(leftgutter + X * i, 0, X, height - bottomgutter).attr({stroke: "none", fill: '#fff', opacity: .2}));
        var rect = blanket[blanket.length - 1];
        (function (x, y, data, lbl, dot) {
            var timer, i = 0;
            rect.hover(function () {
                clearTimeout(leave_timer);
                var side = "right";
                if (x + frame.getBBox().width > width) {
                    side = "left";
                }
                var ppp = r.popup(x, y, label, side, 1),
                    anim = Raphael.animation({
                        path: ppp.path,
                        transform: ["t", ppp.dx, ppp.dy]
                    }, 200 * is_label_visible);
                lx = label[0].transform()[0][1] + ppp.dx;
                ly = label[0].transform()[0][2] + ppp.dy;
                frame.show().stop().animate(anim);

                var date = new Date(lbl * 1000),
                    day = ( date.getDate() < 10 ? '0' : '' ) + date.getDate(),
                    month = ( date.getMonth() + 1 < 10 ? '0' : '' ) + (date.getMonth() + 1),
                    year = date.getFullYear();

                label[0].attr({text: data + "× Spam"}).show().stop().animateWith(frame, anim, {transform: ["t", lx, ly]}, 200 * is_label_visible);
                label[1].attr({text: ( day + '.' + month + '.' + year ) }).show().stop().animateWith(frame, anim, {transform: ["t", lx, ly]}, 200 * is_label_visible);
                dot.attr("r", 6);
                is_label_visible = true;
            }, function () {
                dot.attr("r", 4);
                leave_timer = setTimeout(function () {
                    frame.hide();
                    label[0].hide();
                    label[1].hide();
                    is_label_visible = false;
                }, 1);
            });
        })(x, y, data[i], labels[i], dot);
    }
    p = p.concat([x, y, x, y]);
    bgpp = bgpp.concat([x, y, x, y, "L", x, height - bottomgutter, "z"]);
    path.attr({path: p});
    bgp.attr({path: bgpp});
    frame.toFront();
    label[0].toFront();
    label[1].toFront();
    blanket.toFront();
})();