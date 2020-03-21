class utils {
  static format(str, ...args) {
    for (var i = 0; i < args.length; i++)
      str = str.replace(new RegExp("\\{" + i + "\\}", "g"), args[i]);
    return str;
  }
  static dateFormat(fmt, date) {
    let ret;
    const opt = {
      "Y+": date.getFullYear().toString(), // 年
      "m+": (date.getMonth() + 1).toString(), // 月
      "d+": date.getDate().toString(), // 日
      "H+": date.getHours().toString(), // 时
      "M+": date.getMinutes().toString(), // 分
      "S+": date.getSeconds().toString() // 秒
      // 有其他格式化字符需求可以继续添加，必须转化成字符串
    };
    for (let k in opt) {
      ret = new RegExp("(" + k + ")").exec(fmt);
      if (ret) {
        fmt = fmt.replace(
          ret[1],
          ret[1].length == 1 ? opt[k] : opt[k].padStart(ret[1].length, "0")
        );
      }
    }
    return fmt;
  }

  static string2Date(dateString) {
    dateString.replace(/-/g, "/");
    return Date.parse(dateString);
  }

  static timestamp2Date(timestamp) {
    return new Date(timestamp * 1000);
  }

  static timestampFormat(timestamp, fmt) {
    let date = utils.timestamp2Date(timestamp);
    return utils.dateFormat(fmt, date);
  }
}

export default utils;
