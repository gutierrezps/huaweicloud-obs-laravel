<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>OBS as Laravel Filesystem</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@2/css/pico.red.min.css" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@2/css/pico.colors.min.css" />
</head>

<body>
  <main class="container">
    <h1>Huawei Cloud OBS as Laravel S3 Filesystem</h1>
    <p>
      This is a demonstration on how to use Huawei Cloud
      <a href="https://www.huaweicloud.com/intl/en-us/product/obs.html" target="_blank">Object Storage Service (OBS)</a>
      as <a href="https://laravel.com/docs/11.x/filesystem#amazon-s3-compatible-filesystems" target="_blank">S3-compatible filesystem</a>
      in Laravel.
    </p>

    @if (session('error'))
      <article class="pico-background-pink-200">
      <a href="#" onclick="this.parentElement.remove()"><b>[X]</b></a>
        {!! session('error') !!}
      </article>
    @endif

    @if (session('success'))
      <article class="pico-background-jade-50">
      <a href="#" onclick="this.parentElement.remove()"><b>[X]</b></a>
        {!! session('success') !!}
      </article>
    @endif

    <article>
      <form action="/upload" method="post" enctype="multipart/form-data">
        @csrf
        <fieldset>
          <label for="base_folder">Base folder (optional)</label>
          <input type="text" name="base_folder" id="base_folder">
        </fieldset>
        <fieldset>
          <label for="file_upload">File to Upload</label>
          <input type="file" name="file_upload" id="file_upload" required>
        </fieldset>
        <input type="submit" value="Upload">
      </form>
    </article>

    <article>
      <table>
        <thead>
          <tr><th>Objects</th><th>Operation</th></tr>
        </thead>
        <tbody>
          @forelse ($files as $file)
            <tr>
              <td>{{ $file }}</td>
              <td>
              <form method="POST" action="/delete">
                  @csrf
                  <input type="hidden" name="_method" value="DELETE">
                  <input type="hidden" name="object_key" value="{{ $file }}">
                  <a href="#" onclick="deleteObject(this, '{{ $file }}')">Delete</a>
              </form>
              </td>
            </tr>
          @empty
            <tr><td colspan="2">No objects in bucket</td></tr>
          @endforelse
        </tbody>
      </table>
    </article>
  </main>
</body>

<script>
  function deleteObject(linkElement, objectKey) {
    if (confirm("Are you sure you want to delete object \"" + objectKey + "\"?")) {
      linkElement.parentElement.submit()
    }
  }
</script>

</html>
