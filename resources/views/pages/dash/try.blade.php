<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>


#myInput {
  box-sizing: border-box;
  background-image: url('searchicon.png');
  background-position: 14px 12px;
  background-repeat: no-repeat;
  font-size: 16px;
  padding: 14px 20px 12px 45px;
  border: none;
  border-bottom: 1px solid #ddd;
}

#myInput:focus {outline: 3px solid #ddd;}

.dropdown {
  position: relative;
  display: inline-block;
}

.dropdown_content {
  position: absolute;
  background-color: #f6f6f6;
  min-width: 230px;
  overflow: auto;
  border: 1px solid #ddd;
  z-index: 1;
}

.dropdown_content a {
  color: black;
  padding: 12px 16px;
  text-decoration: none;
  display: block;
}

#myDropdown {
	display: none;
}

</style>
</head>
<body>

<h2>Search/Filter Dropdown</h2>
<p>Click on the button to open the dropdown menu, and use the input field to search for a specific dropdown link.</p>

<div class="dropdown">

  <input type="text" placeholder="Search.." id="myInput" onkeyup="filterFunction()">
  <div id="myDropdown" class="dropdown_content" onselect="myFunction()">
    <a href="#about">About</a>
    <a id="selItem" onclick="selFunction()">Base</a>
    <a href="#blog">Blog</a>
    <a href="#contact">Contact</a>
    <a href="#custom">Custom</a>
    <a href="#support">Support</a>
    <a href="#tools">Tools</a>
  </div>
</div>

<script>
/* When the user clicks on the button,
toggle between hiding and showing the dropdown content */
function myFunction() {
  var drp = document.getElementById("myDropdown");
  drp.style.display = "none";
}

function selFunction() {
  var selItem = document.getElementById("selItem");
  document.getElementById("myInput").value = selItem.innerHTML;
  document.getElementById("myDropdown").style.display = "none";
}

function filterFunction() {

  document.getElementById("myDropdown").style.display = "block";
  
  var input, filter, ul, li, a, i;
  input = document.getElementById("myInput");
  filter = input.value.toUpperCase();
  div = document.getElementById("myDropdown");
  a = div.getElementsByTagName("a");
  for (i = 0; i < a.length; i++) {
    txtValue = a[i].textContent || a[i].innerText;
    if (txtValue.toUpperCase().indexOf(filter) > -1) {
      a[i].style.display = "";
    } else {
      a[i].style.display = "none";
    }
  }
}
</script>

</body>
</html>

