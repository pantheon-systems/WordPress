function wpaeValidateBraces(str) {
    var i, ch, expectedBracket;

    var temp = [];
    var len = str.length;

    var openingBrackets = ['[', '{', '('];
    var closingBrackets = [']', '}', ')'];

    for (i = 0; i < len; i++) {
        ch = str[i];

        if (openingBrackets.indexOf(ch) > -1) {
            temp.push(ch);
        } else if (closingBrackets.indexOf(ch) > -1) {

            expectedBracket = openingBrackets[closingBrackets.indexOf(ch)];
            if (temp.length === 0 || (temp.pop() !== expectedBracket)) {
                return false;
            }

        } else {
            // Ignore the characters which do not match valid Brackets symbol
            continue;
        }
    }

    return (temp.length === 0);
}