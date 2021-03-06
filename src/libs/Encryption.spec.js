import path from 'path'
import assert from 'assert'
import Encryption from './Encryption.js'

describe('Encryption', function() {
  const enc = new Encryption({secret: 'abc123'})
  const originalText = 'test123'
  let cipherText, iv
  let plainText
  let hash

  describe('#encrypt()', function() {
    it(`should encrypt string without issue`, async () => {
      const obj = await enc.encrypt(originalText)
      cipherText = obj.cipherText
      iv = obj.iv
      assert.equal(typeof cipherText, 'string')
    })
  })

  describe('#decrypt()', function() {
    it(`should decrypt cipher string without issue`, async () => {
      plainText = await enc.decrypt(cipherText, iv)
      assert.equal(typeof plainText, 'string')
      assert.equal(plainText, originalText)
    })
  })

  describe('#stringToHash()', function() {
    it(`should hash string without issue`, () => {
      hash = enc.stringToHash(plainText || originalText)
      assert.equal(typeof hash, 'string')
    })
  })

  describe('#fileToHash()', function() {
    it(`should hash file contents without issue`, async () => {
      await enc.fileToHash(path.join(__dirname, 'Encryption.js'))
    })
  })

  describe('#hashPassword() and #comparePassword()', function() {
    let plainPassword = 'test123'
    let hashedPassword

    it(`hashPassword should hash a password as expected`, async () => {
      hashedPassword = await Encryption.hashPassword(plainPassword)
      assert.equal(true, plainPassword != hashedPassword)
      assert.equal(true, hashedPassword.length > 0)
    })

    it(`comparePassword should compare hash with plain password correctly`, async () => {
      const matches = await Encryption.comparePassword(plainPassword, hashedPassword)
      assert.equal(true, matches)
    })
  })
})
