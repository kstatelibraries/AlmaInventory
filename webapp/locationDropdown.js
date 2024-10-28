// START -- Enable user dropdown location validation -- added by K-State Libraires 06/2023

$(document).ready(function(){
  var libraryObject = {
    "Hale": ["cmc", "currper", "dowdis", "dowref", "gov", "govks", "govmap", "govmfile", "govmindex", "govoffmap", "halers", "juv", "main", "mediacoll", "medialp", "mediaover", "mic", "micref", "microfilm", "musart", "musartover", "musartref", "muscd", "muscdover", "musscore", "over", "overplus", "preslab", "ref", "sortmain", "sunderland", "tech", "techjstor", "techserial"],
    "Vetmed": ["vetm", "vetmrs", "vetmhist", "vetmjuv", "vetmnew", "vetmover", "vetmper", "vetmref", "vetmspec"],
    "Math/Physics": ["phys"],
    "Weigel": ["arch", "archoverrs", "archmapc", "archmedia", "archnew", "archover", "archper", "archref", "archspec"]
  }

  window.onload = function () {
    var librarySel = document.getElementById("libraryCode");
    var locationSel = document.getElementById("locationCode");

    for (var x in libraryObject) {
      librarySel.options[librarySel.options.length] = new Option(x, x);
    }
    librarySel.onchange = function() {
      var selectedLibrary = libraryObject[this.value];
      locationSel.length = 1;
      selectedLibrary.forEach(function(location) {
        locationSel.options[locationSel.options.length] = new Option(location, location);
      })
    }
  }
});

// END -- Enable user dropdown location validation -- added by K-State Libraires 06/2023