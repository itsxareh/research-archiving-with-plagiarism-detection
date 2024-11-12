import nltk
from nltk.tokenize import sent_tokenize
nltk.download('punkt')


def split_into_sentences(text):
    sentences =  sent_tokenize(text)
    return [sentence for sentence in sentences if len(sentence.split()) >= 8]

sentence = split_into_sentences("Ang mga kasanayan ay marapat na angkop sa panahon at henerasyon ng mga mag-aaral upang lubos itong maging mahalaga (Dimock, 2019). Esensiyal sa ugnayang kasanayangkomunikatibo ang kaalaman sa wika na magbibigay-kasanayan sa talastasang kadikit ng iba pang mga tema. Samakatuwid, madaling tukuyin kung paano maiisa-isa ang mga kasanayan subalit mapanghamon kung paano ito masisigurong matatamo at matutuhan. Sa ganitong konteksto, marapat na maging makabago at inobatibo ang isang estratehiya o anomang pagsasanay upang bigyang-katuturan at kabuluhan ang isang lunsarang aralin. Kaya, kapag nagtuturo tayo, dapat tayong makabuo ng mga bago at malikhaing paraan upang gawing kapana-panabik at makabuluhan ang mga aralin. Sa panahon ng pandemya, nagkaroon ng malaking pagbabago sa kung paano kami nagtuturo ng wika at mga kuwento, tulad ng ipinakita sa pag-aaral ni Guvenc (2022).")
print(sentence)
print(len(sentence))