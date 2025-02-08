function Encryption() {
  
    this.KeyIv =()=>{
      return 'xxxxxxxxxxxx4xxxyxxxxxxxxxxxxxxx'
      .replace(/[xy]/g, function (c) {
        const r = Math.random() * 16 | 0,
          v = c == 'x' ? r : (r & 0x3 | 0x8);
        return v.toString(16);
      });
    }
  
    this.replaceSpecialChars=(e)=>{
      return e
          .replace(/=/g, ',')  // Replace "=" with ","
          .replace(/\+/g, '-')  // Replace "+" with "-"
          .replace(/\//g, '_');  // Replace "/" with "_"
    }
  
    this.encrypt_aes_keyiv=(kv)=>{
      let publicKey = `-----BEGIN PUBLIC KEY-----MIIBCgKCAQEAukSqgXt9DsAJuwvrRrDhHwWzSRDwjCmRlPc5ssafWAZnB8ab2gfLRABv0MBwKtCxNrMbncS4Ic8/W05ISBGtkkphVbt4JM22yZAGWqD+Nszk8ESfPMbhWaLF64Egt/vGWZFwa4qbdrXEhiW5nb8jrc4wE+pv4eDOGziALoBtEU0cjeGWQhUMsb1behS0Tzbq0XY39e3pru1jBBK3c/PCp8tuPUl336AopK+8chIqDipCDoNg2WUXjQ6IAgWnc4O44q9mo7naU2nHigUCtdarTfoeOLdKMUAQTY05NGNuN5G+0ma9aXjuIJGzX9vQCBy9GnchJaHvtMWXWMRWTiucdQIDAQAB-----END PUBLIC KEY-----`;  // Replace with your actual public key
      
      // new
      // let publicKey = `-----BEGIN PUBLIC KEY-----MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAnBBNCJJXHIRS8aAqmRVyAvxhzxcrp/Ic5/n9NEHb+1WT7KrC5skG3jhIuWviSOohMdagtcPvEe4Y6U5mxuegFUIa1fw7a8Uck3rQIa/TcssJvCOdQbj4l+DAeDzLmwNiy9mL56Cuf7ivLePfl7GETFZB50vRHbexjyIg+IdP5AJKoYxV8erzHTStXqZdLp2b1KG9/sny69Cb4yr/duwyTn+eT1VEBKJBeGUh4UTGt6owdAh7UIZ4Al9aZH3NPMJktZfumGGft5ZEXEpCsqFT68XkWiTQDl8mWAv8zVQ10Kt6gKx5AAY3277Nk9DnWsygqYSag/IGzjzFFaqYx4WLEQIDAQAB-----END PUBLIC KEY-----`;  // Replace with your actual public key
      
      let encryptor = new JSEncrypt({ default_key_size: "2048" });
      encryptor.setPublicKey(publicKey);
      let ciphertext = encryptor.encrypt(kv);
      return ciphertext.toString();
    }
  
    this.encrypt_payload=(input, kv)=>{
     
        let kvq = kv.substring(0, 16);
        let ivq = kv.substring(16, 32);
  
        let encryptString = CryptoJS.AES.encrypt(CryptoJS.enc.Utf8.parse(input), CryptoJS.enc.Utf8.parse(kvq), {
            keySize: 128 / 8,
            iv: CryptoJS.enc.Utf8.parse(ivq),
            mode: CryptoJS.mode.CBC,
            padding: CryptoJS.pad.Pkcs7,
        });
        return encryptString.toString();
    }
  
    return this;
  
  }