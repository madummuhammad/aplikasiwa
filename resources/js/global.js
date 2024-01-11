
export let Global = () => {
    const myFunction = () => {
        console.log('asdf')
    }

    const env = 'local';

    var urlCheckout = 'http://127.0.0.1:8000/landing/';
    const formatNumber = (number) => {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR'
        }).format(number);
    }
    // Can be local, development or production
    return {
        myFunction,
        env,
        urlCheckout,
        formatNumber
    }
}

// export default Global
