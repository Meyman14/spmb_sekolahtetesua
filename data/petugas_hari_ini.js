/*
  Data petugas per tanggal (22 Juni 2026 — 4 Juli 2026)
  Expose global `PetugasHariIni` with methods:
    - getByDate(dateString|Date) -> Array of names
    - getToday() -> Array for current date
    - all() -> full data object

  Usage example (in dashboard):
    <script src="data/petugas_hari_ini.js"></script>
    <script>
      const list = PetugasHariIni.getByDate('2026-06-22');
      console.log(list);
    </script>
*/
(function (global) {
  'use strict';

  var data = {
    '2026-06-07': ['Agustina Jambak','Alimmarwin Zalukhu','Anurni Lestari Daeli'],
    '2026-06-22': ['Agustina Jambak','Alimmarwin Zalukhu','Anurni Lestari Daeli'],
    '2026-06-23': ['Faebuadodo Hia','Kasih Karuniasti Daeli','Lidia Lismawati Lase'],
    '2026-06-24': ['Maulidanur Aceh','Menifati Gulo','Merlina Zebua'],
    '2026-06-25': ['Nerius Maruhawa','Mey Lestari Daeli','Niat Asri Natalia Daeli'],
    '2026-06-26': ['Saribadi Hia','Nirani Giawa','Niska Trita Olivia Zalukhu'],
    '2026-06-27': ['Theresia Daeli','Yanania Gulo','Yunidar Nazara'],
    '2026-06-28': [],
    '2026-06-29': ['Ervinta Wati Malau','Faebuadodo Hia','Kasih Karuniasti Daeli'],
    '2026-06-30': ['Lidia Lismawati Lase','Maulidanur Aceh','Menifati Gulo'],
    '2026-07-01': ['Merlina Zebua','Mey Lestari Daeli','Nerius Maruhawa'],
    '2026-07-02': ['Niat Asri Natalia Daeli','Nirani Giawa','Niska Trita Olivia Zalukhu'],
    '2026-07-03': ['Saribadi Hia','Theresia Daeli','Yanania Gulo'],
    '2026-07-04': ['Yunidar Nazara','Agustina Jambak','Alimmarwin Zalukhu']
  };

  function normalizeDate(d) {
    if (!d) {
      var now = new Date();
      return now.toISOString().slice(0,10);
    }
    if (d instanceof Date) return d.toISOString().slice(0,10);
    // assume string already YYYY-MM-DD or other parseable format
    // try to construct Date then return YYYY-MM-DD
    var s = String(d).trim();
    // if already looks like YYYY-MM-DD return as-is
    if (/^\d{4}-\d{2}-\d{2}$/.test(s)) return s;
    var dt = new Date(s);
    if (isNaN(dt)) return s; // fallback: return original
    return dt.toISOString().slice(0,10);
  }

  var API = {
    getByDate: function(date) {
      var key = normalizeDate(date);
      return data[key] ? data[key].slice(0) : [];
    },
    getToday: function() {
      return this.getByDate();
    },
    all: function() {
      // shallow copy
      return Object.assign({}, data);
    }
  };

  global.PetugasHariIni = API;
})(window);
