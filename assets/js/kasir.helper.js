
	function formatAngka(angka) {
		if (typeof(angka) != 'string') angka = angka.toString();
		var reg = new RegExp('([0-9]+)([0-9]{3})');
		while(reg.test(angka)) angka = angka.replace(reg, '$1.$2');
		return angka;
    }
    function ucfirst(text)
	{
	    return text.charAt(0).toUpperCase() + text.substr(1);
	}
	function ucwords(text)
	{
		return text.toLowerCase().replace(/\b[a-z]/g, function(letter) {
		    return letter.toUpperCase();
		});
	}