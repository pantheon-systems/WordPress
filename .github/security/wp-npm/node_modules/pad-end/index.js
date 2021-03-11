'use strict';

module.exports = function (string, maxLength, fillString) {

  if (string == null || maxLength == null) {
    return string;
  }

  var result    = String(string);
  var targetLen = typeof maxLength === 'number'
    ? maxLength
    : parseInt(maxLength, 10);

  if (isNaN(targetLen) || !isFinite(targetLen)) {
    return result;
  }


  var length = result.length;
  if (length >= targetLen) {
    return result;
  }


  var filled = fillString == null ? '' : String(fillString);
  if (filled === '') {
    filled = ' ';
  }


  var fillLen = targetLen - length;

  while (filled.length < fillLen) {
    filled += filled;
  }

  var truncated = filled.length > fillLen ? filled.substr(0, fillLen) : filled;

  return result + truncated;
};
