/**
 * @param {Integer} R
 * @param {Integer} G
 * @param {Integer} B
 * @returns {String}
 */
ko.dt.rgbToHex = function (R, G, B) {
    return ko.dt.toHex(R) + ko.dt.toHex(G) + ko.dt.toHex(B)
};
/**
 * @param {Integer} n
 * @returns {String}
 */
ko.dt.toHex = function (n) {
    n = parseInt(n, 10);
    if (isNaN(n))
        return "00";
    n = Math.max(0, Math.min(n, 255));
    return "0123456789ABCDEF".charAt((n - n % 16) / 16)
            + "0123456789ABCDEF".charAt(n % 16);
};
/**
 * 
 * @param {String} browser (linear or webkit)
 * @param {Integer} charCount The number of characters
 * @returns {String}
 */
ko.dt.grade = function (browser, charCount) {
    var hexString, response, val;
    switch (true) {
        case(charCount >= 600):
            val = 0;//creates red
            break;
        case (charCount >= 0 && charCount > 400 && charCount < 600):
            val = 255 - (((charCount - 400) / 200) * 255);
            break;
        default:
            val = 255; //creates white
            break;
    }
    hexString = ko.dt.rgbToHex(255, val, val);
    switch (browser) {
        case "linear":
            response = "linear-gradient(to right, #ffffff, #" + hexString + ")"
            break;
        case "webkit":
            response = "-webkit-linear-gradient(to right, #ffffff , #" + hexString + ")"
            break;
    }
    return response;
};