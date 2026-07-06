<!DOCTYPE html>
<html>
<body>

<?php
  // This is a single-line comment
  echo "Meow!"; 
?>

<div x-data="{ count: 0 }" class="p-4 bg-white rounded shadow">
    <button @click="count++" class="px-4 py-2 bg-indigo-600 text-white rounded">
        Increment
    </button>
    <span class="ml-3 text-lg font-bold" x-text="count"></span>
</div>

</body>
</html>
