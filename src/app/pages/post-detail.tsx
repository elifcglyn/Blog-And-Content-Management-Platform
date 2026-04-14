import * as React from "react";
import { Link, useParams } from "react-router"; // React-router-dom kullanıyorsanız "react-router-dom" yapın.
import { 
  History, ArrowLeft, Sparkles, CheckCircle2, 
  User, Heart, MessageCircle, Share2, Check 
} from "lucide-react";
// mockPosts'u import etmiyoruz çünkü artık gerçek veri kullanacağız!
import { Avatar, AvatarFallback, AvatarImage } from "../components/ui/avatar";
import { BookmarkButton } from "../components/bookmark-button";

export function PostDetailPage() {
  const { slug } = useParams(); // URL'den tıklanan yazının ID'sini (slug) alıyoruz
  const [post, setPost] = React.useState<any>(null); // Veritabanından gelecek yazı
  const [loading, setLoading] = React.useState(true); // Yükleniyor durumu
  
  const [activeVersion, setActiveVersion] = React.useState<any>(null);
  const [isLiked, setIsLiked] = React.useState(false);
  const [likes, setLikes] = React.useState(0);
  const [isFollowing, setIsFollowing] = React.useState(false);
  const [showComments, setShowComments] = React.useState(false);
  const [comments, setComments] = React.useState<any[]>([]);
  const [newComment, setNewComment] = React.useState("");

  // Yorumları Veritabanından Çeken Fonksiyon
  const yorumlariGetir = () => {
    if (!post?.id) return;
    fetch(`http://localhost/Blog-And-Content-Management-Platform/api/yorumlar.php?yazi_id=${post.id}`)
      .then(res => res.json())
      .then(data => setComments(data))
      .catch(err => console.error("Yorum çekme hatası:", err));
  };

  // Yazı yüklendiğinde yorumları da otomatik getir
  React.useEffect(() => {
    yorumlariGetir();
  }, [post?.id]);

  // 🚀 Yeni Yorum Gönderme Fonksiyonu
  const yorumGonder = () => {
    if (!newComment.trim()) return;
    
    fetch("http://localhost/Blog-And-Content-Management-Platform/api/yorumlar.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        yazi_id: post.id,
        kullanici_id: 1, // Şimdilik sen (1 id'li yazar) yorum yapıyor varsayıyoruz
        icerik: newComment
      })
    })
    .then(res => res.json())
    .then(data => {
      setNewComment(""); // Kutuyu temizle
      yorumlariGetir(); // Yorumları yenile ki yazdığımız anında ekrana düşsün!
    });
  };
  // 🚀 SAYFA AÇILDIĞINDA PHP'DEN YAZIYI ÇEK
  React.useEffect(() => {
    // Tüm yazıları getir (Daha gelişmiş bir sistemde sadece 1 yazıyı getiren bir PHP yazılabilir, şimdilik buradan filtreliyoruz)
    fetch("http://localhost/Blog-And-Content-Management-Platform/api/yazilari_getir.php")
      .then((res) => res.json())
      .then((data) => {
        // Tıklanan yazıyı (slug ile eşleşen ID'yi) bul
        const bulunanYazi = data.find((p: any) => p.id.toString() === slug);
        
        if (bulunanYazi) {
          // PHP'den gelen Türkçe veriyi React'ın beklediği İngilizce formata çevir
          const formatliYazi = {
            id: bulunanYazi.id.toString(),
            title: bulunanYazi.baslik,
            content: bulunanYazi.icerik,
            category: "Genel", // Kategori isimlerini ID'den çevirmek için daha sonra ayrı bir fonksiyon yazılabilir
            coverImage: bulunanYazi.kapak_resmi || "https://images.unsplash.com/photo-1555066931-4365d14bab8c?auto=format&fit=crop&q=80&w=2000",
            readingTime: "3",
            author: { name: "Emirhan", avatar: "https://ui-avatars.com/api/?name=Emirhan" },
            // Şimdilik sahte bir versiyon geçmişi koyuyoruz (Eğer veritabanına "Sürüm Notu" eklemediysek sayfa çökmesin diye)
            versions: [
              { version: "1.0", date: bulunanYazi.yayin_tarihi, changeNote: "İlk yayın", isCurrent: true }
            ]
          };
          setPost(formatliYazi);
          setActiveVersion(formatliYazi.versions[0]);
        }
        setLoading(false);
      })
      .catch((err) => {
        console.error("Yazı çekilirken hata:", err);
        setLoading(false);
      });
  }, [slug]);

  if (loading) {
    return <div className="flex-1 flex items-center justify-center min-h-screen"><p>Yükleniyor...</p></div>;
  }

  if (!post) {
    return (
      <div className="flex-1 flex flex-col items-center justify-center min-h-screen text-slate-500">
        <h2 className="text-3xl font-serif italic mb-4">Yazı bulunamadı</h2>
        <Link to="/" className="text-teal-600 hover:underline font-medium">Ana Sayfaya Dön</Link>
      </div>
    );
  }

  // HTML İçeriğini Güvenli ve Temiz Şekilde Basma Fonksiyonu
  const displayContent = post.content;

  
  return (
    <div className="flex-1 flex flex-col bg-white dark:bg-slate-950 font-sans min-h-screen">
      
      {/* KOD BLOKLARI İÇİN ÖZEL STİL */}
      <style dangerouslySetInnerHTML={{__html: `
        .prose pre { background: #0f172a !important; color: #f8fafc !important; padding: 2rem !important; border-radius: 1.5rem !important; }
        .prose code { color: #0d9488 !important; font-weight: 700 !important; }
      `}} />

      <main className="container mx-auto max-w-6xl py-12 px-6 animate-in fade-in duration-700">
        
        {/* ÜST GEZİNTİ VE KAYDET BUTONU */}
        <div className="flex justify-between items-center mb-10">
          <Link to="/" className="inline-flex items-center gap-2 text-slate-400 hover:text-teal-600 transition-colors text-sm font-medium">
            <ArrowLeft className="w-4 h-4" /> Ana Sayfaya Dön
          </Link>
          <div className="flex items-center gap-2">
            <span className="text-[10px] font-bold uppercase tracking-widest text-slate-400 mr-2">Kitaplığına Ekle</span>
            <BookmarkButton /> {/* 🚀 İŞTE BURADA! */}
          </div>
        </div>

        <div className="grid grid-cols-1 lg:grid-cols-12 gap-16">
          
          {/* SOL TARAF: İÇERİK */}
          <div className="lg:col-span-8 space-y-10">
            <header>
              <div className="flex items-center gap-3 text-teal-600 font-bold text-[10px] uppercase tracking-[0.2em] mb-6">
                <Sparkles className="w-4 h-4" /> <span>{post.category}</span>
              </div>
              <h1 className="font-serif text-5xl md:text-7xl italic tracking-tighter text-slate-950 dark:text-white leading-[1.1] mb-8">
                {post.title}
              </h1>
              
              <div className="flex items-center justify-between py-6 border-y border-slate-100 dark:border-slate-800">
                <div className="flex items-center gap-4">
                  <Avatar className="h-12 w-12 border-2 border-white shadow-sm">
                    <AvatarImage src={post.author.avatar} />
                    <AvatarFallback><User /></AvatarFallback>
                  </Avatar>
                  <div>
                    <div className="flex items-center gap-3">
                      <p className="font-bold text-slate-900 dark:text-white">{post.author.name}</p>
                      <button 
                        onClick={() => setIsFollowing(!isFollowing)}
                        className={`text-[10px] font-bold uppercase tracking-widest px-3 py-1 rounded-full transition-all ${
                          isFollowing ? "bg-slate-100 text-slate-400" : "bg-teal-50 text-teal-700 hover:bg-teal-100"
                        }`}
                      >
                        {isFollowing ? <span className="flex items-center gap-1"><Check className="w-3 h-3"/> Takipte</span> : "+ Takip Et"}
                      </button>
                    </div>
                    <p className="text-xs text-slate-400">{post.readingTime} dk okuma • {activeVersion?.version} sürümü</p>
                  </div>
                </div>
                <button className="p-3 hover:bg-slate-50 rounded-full transition-colors text-slate-400"><Share2 className="w-5 h-5" /></button>
              </div>
            </header>

            <article className="prose prose-lg max-w-none dark:prose-invert">
              <div className="w-full aspect-video rounded-[3rem] overflow-hidden mb-12 shadow-2xl shadow-teal-500/5 border border-slate-100">
                <img src={post.coverImage} className="w-full h-full object-cover" />
              </div>
              <div 
                className="font-sans text-slate-700 dark:text-slate-300 leading-relaxed"
                dangerouslySetInnerHTML={{ __html: displayContent }} 
              />
            </article>

            {/* ETKİLEŞİM PANELİ */}
            <div className="flex items-center gap-8 py-8 border-t border-slate-100 dark:border-slate-800">
              <button 
                onClick={() => { setIsLiked(!isLiked); setLikes(isLiked ? likes - 1 : likes + 1); }}
                className={`flex items-center gap-3 transition-all group ${isLiked ? 'text-red-500' : 'text-slate-400 hover:text-red-500'}`}
              >
                <div className={`p-4 rounded-full transition-colors ${isLiked ? 'bg-red-50' : 'group-hover:bg-red-50'}`}>
                  <Heart className={`w-7 h-7 ${isLiked ? 'fill-current' : ''}`} />
                </div>
                <span className="text-lg font-bold">{likes} Beğeni</span>
              </button>

              <button 
                onClick={() => setShowComments(!showComments)}
                className="flex items-center gap-3 text-slate-400 hover:text-teal-600 group"
              >
                <div className="p-4 rounded-full group-hover:bg-teal-50 transition-colors">
                  <MessageCircle className="w-7 h-7" />
                </div>
                {/* 🚀 YENİ: Sabit 24 yerine gerçek yorum sayısını gösteriyoruz */}
                <span className="text-lg font-bold">{comments.length} Yorum</span>
              </button>
            </div>

            {/* 💬 GERÇEK YORUM SİSTEMİ BURAYA EKLENDİ */}
            {showComments && (
              <div className="py-8 border-t border-slate-100 dark:border-slate-800 animate-in slide-in-from-top-4 duration-500">
                <h3 className="text-2xl font-serif italic mb-6">Yorumlar ({comments.length})</h3>
                
                {/* Yorum Yazma Kutusu */}
                <div className="flex gap-4 mb-10">
                  <Avatar className="h-10 w-10"><AvatarImage src="https://ui-avatars.com/api/?name=Emirhan" /></Avatar>
                  <div className="flex-1 space-y-3">
                    <textarea 
                      value={newComment}
                      onChange={(e) => setNewComment(e.target.value)}
                      placeholder="Hikaye hakkında ne düşünüyorsun?"
                      className="w-full bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-4 text-sm focus:ring-2 ring-teal-500 outline-none min-h-[100px] resize-y"
                    />
                    <div className="flex justify-end">
                      <button 
                        onClick={yorumGonder}
                        disabled={!newComment.trim()}
                        className="bg-teal-600 text-white font-bold text-xs uppercase tracking-widest px-6 py-3 rounded-full hover:bg-teal-700 disabled:opacity-50 transition-colors"
                      >
                        Yorumu Paylaş
                      </button>
                    </div>
                  </div>
                </div>

                {/* Yorumları Listeleme Alanı */}
                <div className="space-y-6">
                  {comments.length > 0 ? (
                    comments.map((yorum: any) => (
                      <div key={yorum.id} className="flex gap-4 p-5 rounded-3xl bg-slate-50/50 dark:bg-slate-900/50 border border-slate-100 dark:border-slate-800">
                        <Avatar className="h-10 w-10">
                          <AvatarImage src={yorum.avatar_url || `https://ui-avatars.com/api/?name=${yorum.ad_soyad}`} />
                          <AvatarFallback><User/></AvatarFallback>
                        </Avatar>
                        <div>
                          <div className="flex items-center gap-2 mb-1">
                            <span className="font-bold text-slate-900 dark:text-white text-sm">{yorum.ad_soyad}</span>
                            <span className="text-xs text-slate-400">• {new Date(yorum.tarih).toLocaleDateString('tr-TR')}</span>
                          </div>
                          <p className="text-slate-600 dark:text-slate-300 text-sm leading-relaxed">{yorum.icerik}</p>
                        </div>
                      </div>
                    ))
                  ) : (
                    <p className="text-center text-slate-400 italic py-4">Henüz yorum yapılmamış. İlk yorumu sen yaz!</p>
                  )}
                </div>
              </div>
            )}
          </div>

          {/* SAĞ TARAF: VERSİYON GEÇMİŞİ */}
          <aside className="lg:col-span-4">
            <div className="sticky top-28 p-8 bg-slate-50 dark:bg-slate-900 rounded-[2.5rem] border border-slate-100 dark:border-slate-800">
              <h3 className="font-serif text-2xl italic mb-8 flex items-center gap-3 text-slate-900 dark:text-white">
                <History className="w-6 h-6 text-teal-600" /> Yazı Geçmişi
              </h3>
              <div className="space-y-4">
                {post.versions.map((ver: any) => {
                  const isActive = activeVersion?.version === ver.version;
                  return (
                    <button
                      key={ver.version}
                      onClick={() => setActiveVersion(ver)}
                      className={`w-full text-left p-5 rounded-3xl transition-all border ${
                        isActive 
                          ? 'bg-white dark:bg-slate-800 border-teal-500 shadow-xl shadow-teal-500/10 scale-[1.03]' 
                          : 'bg-transparent border-transparent hover:bg-white/50 text-slate-500'
                      }`}
                    >
                      <div className="flex justify-between items-center mb-2">
                        <span className={`font-bold ${isActive ? 'text-teal-600' : 'text-slate-900 dark:text-white'}`}>v{ver.version}</span>
                        {isActive && <CheckCircle2 className="w-4 h-4 text-teal-500" />}
                      </div>
                      <p className="text-[10px] uppercase tracking-widest text-slate-400 mb-2">{new Date(ver.date).toLocaleDateString("tr-TR")}</p>
                      <p className="text-xs leading-relaxed italic">{ver.changeNote}</p>
                    </button>
                  );
                })}
              </div>
            </div>
          </aside>
        </div>
      </main>
    </div>
  );
}