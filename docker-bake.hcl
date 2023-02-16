group "default" {
    targets = ["suomidigi"]
}

target "suomidigi" {
    platforms = ["linux/amd64"]
    context = "."
    tags = ["suomidigi:prod"]
}
