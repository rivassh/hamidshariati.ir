# Unity — Local Build Checklist

| Platform | Unity setting | Server path |
|----------|---------------|-------------|
| WebGL | Build → WebGL → Build | `/opt/games/<game>/upload/` |
| Android | Build → Android → APK | `/opt/games/<game>/upload/app.apk` |

Compression: **Gzip** (matches nginx `gzip_static`).

Do **not** install Unity on this server.
