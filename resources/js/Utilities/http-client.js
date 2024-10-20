const post = (endpoint, body, csrf) => {
    return fetch(endpoint, {
        method: 'POST',
        body: JSON.stringify({
            "_token": csrf,
            ...body
        }),
        headers: {
            "Content-Type": "application/json",
            "Accept": "application/json"
        }
    })
}

export {
    post
}