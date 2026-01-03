export const goTo = (endpoint="") => {
    window.location.href = "/"+endpoint
}

export const rand = () => {
    return Math.random() < 0.5 ? 'multiple_choice' : 'write'
}
