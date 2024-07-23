const { schnorr } = require("bcrypto");

// Function to sign a message using the provided private key
const signMessage = async (privateKeyHex, messageHex) => {
    const privateKey = Buffer.from(privateKeyHex, "hex");
    const message = Buffer.from(messageHex, "hex");

    try {
        const signature = await schnorr.sign(message, privateKey);
        const signatureHex = signature.toString("hex");

        return {
            status: "success",
            signature: signatureHex,
        };
    } catch (error) {
        return {
            status: "error",
            message: error.message,
        };
    }
};

// Function to verify a signature using the provided public key
const verifySignature = async (publicKeyHex, messageHex, signatureHex) => {
    const publicKey = Buffer.from(publicKeyHex, "hex");
    const message = Buffer.from(messageHex, "hex");
    const signature = Buffer.from(signatureHex, "hex");

    try {
        // Remove leading byte ('02' / '03') from ECDSA key
        if (publicKey.byteLength === 33) {
            publicKey = publicKey.subarray(1);
        }

        const isValid = await schnorr.verify(message, signature, publicKey);
        return {
            status: "success",
            isValid: isValid,
        };
    } catch (error) {
        return {
            status: "error",
            message: error.message,
        };
    }
};

// Function to parse command line arguments and call the appropriate function
const main = async () => {
    const args = process.argv.slice(2);

    if (args.length < 3) {
        console.error(
            JSON.stringify({
                status: "error",
                message: "Usage: npm start <mode> <parameters>",
            })
        );
        process.exit(1);
    }

    const mode = args[0];
    let result;

    if (mode === "sign" && args.length === 3) {
        const [privateKeyHex, messageHex] = args.slice(1);
        result = await signMessage(privateKeyHex, messageHex);
    } else if (mode === "verify" && args.length === 4) {
        const [publicKeyHex, messageHex, signatureHex] = args.slice(1);
        result = await verifySignature(publicKeyHex, messageHex, signatureHex);
    } else {
        console.error(
            JSON.stringify({
                status: "error",
                message: `Usage: npm start ${mode} <parameters>.\nFor 'sign': npm start sign <privateKeyHex> <messageHex>.\nFor 'verify': npm start verify <publicKeyHex> <messageHex> <signatureHex>.`,
            })
        );
        process.exit(1);
    }

    console.log(JSON.stringify(result));
};

main();
