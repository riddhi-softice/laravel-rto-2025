
function encryptData(allParams){
    const encryption = new Encryption();
    let kv = encryption.KeyIv();
    
    // const inputText={
    //     "RegNo": document.getElementById("registration").value,
    //     "Mobile":  document.getElementById("Mobile").value,
    //     "Email":  document.getElementById("Email").value,
    //     "Product":  document.getElementById("Product").value,
    //     "Client":  document.getElementById("Client").value,
    //     "Info1":  document.getElementById("Info1").value,
    //     "Info2":  document.getElementById("Info2").value,
    // };
    
    let inputText = allParams;

    let payload = encryption.encrypt_payload(JSON.stringify(inputText), kv);
    let d = encryption.replaceSpecialChars(payload);
    
    let encrsa = encryption.encrypt_aes_keyiv(kv);
    let k = encryption.replaceSpecialChars(encrsa);
    
    //    document.getElementById("ilurl").textContent = "https://echannel.insurancearticlez.com/digital-web/v2.0/webclient/motor-insurance?d=" + d + "&k=" + k;  
    let redirectUrl = "https://echannel.insurancearticlez.com/digital-web/v2.0/webclient/motor-insurance?d=" + d + "&k=" + k;
    window.location.href = redirectUrl;    
}